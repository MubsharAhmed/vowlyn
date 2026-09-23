<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\OutreachEmail;
use App\Models\EmailSuppression;
use App\Models\OutreachCampaign;
use App\Models\OutreachCampaignStep;
use App\Models\OutreachEnrollment;
use App\Models\OutreachMessage;
use App\Models\OutreachProspect;
use App\Support\MailDelivery;
use App\Support\OutreachQuota;
use App\Support\OutreachWindow;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

/**
 * Delivers the next step of one person's sequence, or explains why it did not.
 *
 * Runs synchronously, like the lead replies, and for a stronger reason: outreach
 * batches are dispatched by a scheduled command, and a queued job would need a
 * worker that somebody has to remember to keep running. When that worker is not
 * running, the panel shows a campaign going out while nothing leaves the
 * building — the failure mode this codebase is built to avoid. Synchronous
 * sending means "sent" always means the mailer accepted it, right then.
 *
 * Every path through this class leaves a row behind, including the refusals.
 * An email that was not sent is the one question a history has to answer.
 */
final class OutreachSender
{
    /** Consecutive failures before a sequence gives up on its own. */
    private const FAILURE_LIMIT = 3;

    /**
     * Send the next due step for one enrollment.
     *
     * Returns the message that was recorded, or null when there was nothing to
     * do (no further steps, or a campaign that is not sending).
     */
    public function send(OutreachEnrollment $enrollment): ?OutreachMessage
    {
        // Scheduled and manual sends share a lock. Recheck the daily allowance
        // inside it so concurrent requests cannot spend the same slot twice.
        return Cache::lock('outreach:send', 300)
            ->block(10, fn (): ?OutreachMessage => $this->sendUnderLock($enrollment));
    }

    private function sendUnderLock(OutreachEnrollment $enrollment): ?OutreachMessage
    {
        $enrollment->refresh();

        if (! $enrollment->isActive()) {
            return null;
        }

        $enrollment->loadMissing(['campaign.steps', 'prospect']);

        $campaign = $enrollment->campaign;
        $prospect = $enrollment->prospect;

        if ($campaign === null || $prospect === null) {
            return null;
        }

        $step = $campaign->stepAt($enrollment->current_step);

        if ($step === null) {
            $enrollment->stop(OutreachEnrollment::STATUS_FINISHED, 'No further steps in this sequence.');

            return null;
        }

        // A campaign that is paused mid-run must stop immediately, not finish
        // the batch it happened to be inside.
        if (! $campaign->isSending()) {
            return $this->record($enrollment, $step, OutreachMessage::STATUS_SKIPPED, 'This campaign is not active.');
        }

        $refusal = $this->refusal($enrollment, $prospect, $step);

        if ($refusal !== null) {
            return $refusal;
        }

        if (! OutreachWindow::isOpen()) {
            $enrollment->update(['next_send_at' => OutreachWindow::nextOpen()]);

            return $this->record(
                $enrollment,
                $step,
                OutreachMessage::STATUS_SKIPPED,
                'The sending window is closed. This step will wait until the next open window.',
            );
        }

        if (OutreachQuota::runAllowance(1, $campaign) === 0) {
            $enrollment->update(['next_send_at' => OutreachWindow::nextOpen()]);

            return $this->record(
                $enrollment,
                $step,
                OutreachMessage::STATUS_SKIPPED,
                'The daily sending limit is reached. This step will wait until the next open window.',
            );
        }

        return $this->deliver($enrollment, $campaign, $prospect, $step);
    }

    /**
     * Reasons not to send, checked in the order that matters most.
     *
     * The opt-out list is consulted before anything else, every single time.
     * It is the one rule that must not be bypassable by a stale list, a changed
     * status, or a campaign scheduled a year ago.
     */
    private function refusal(
        OutreachEnrollment $enrollment,
        OutreachProspect $prospect,
        OutreachCampaignStep $step,
    ): ?OutreachMessage {
        $suppression = EmailSuppression::query()->where('email', $prospect->email)->first();

        if ($suppression !== null) {
            $enrollment->stop(
                OutreachEnrollment::STATUS_UNSUBSCRIBED,
                'On the opt-out list: '.EmailSuppression::reasonLabel($suppression->reason).'.',
            );

            if (! $prospect->blockedFromContact()) {
                $prospect->update(['status' => OutreachProspect::STATUS_UNSUBSCRIBED]);
            }

            return $this->record(
                $enrollment,
                $step,
                OutreachMessage::STATUS_SKIPPED,
                'Address is on the opt-out list ('.EmailSuppression::reasonLabel($suppression->reason).').',
            );
        }

        if ($prospect->blockedFromContact()) {
            $reason = 'Prospect is marked “'.$prospect->statusLabel().'”.';

            $enrollment->stop(match ($prospect->status) {
                OutreachProspect::STATUS_UNSUBSCRIBED => OutreachEnrollment::STATUS_UNSUBSCRIBED,
                OutreachProspect::STATUS_BOUNCED => OutreachEnrollment::STATUS_BOUNCED,
                default => OutreachEnrollment::STATUS_NOT_INTERESTED,
            }, $reason);

            return $this->record($enrollment, $step, OutreachMessage::STATUS_SKIPPED, $reason);
        }

        // The cooldown is a delay, not an ending: the same person may be
        // approached again by a later campaign, just not in the same month.
        $available = $this->cooldownEndsAt($prospect);

        if ($available !== null && $available->isFuture()) {
            $enrollment->update(['next_send_at' => $available]);

            return $this->record(
                $enrollment,
                $step,
                OutreachMessage::STATUS_SKIPPED,
                sprintf(
                    'Contacted recently — waiting out the %d-day cooldown until %s.',
                    (int) config('outreach.cooldown_days'),
                    $available->format('j M Y'),
                ),
            );
        }

        return null;
    }

    private function deliver(
        OutreachEnrollment $enrollment,
        OutreachCampaign $campaign,
        OutreachProspect $prospect,
        OutreachCampaignStep $step,
    ): OutreachMessage {
        $copy = $step->renderFor($prospect);
        $transport = MailDelivery::transport();

        // Written first, so a crash mid-send still leaves evidence that this
        // person was about to be emailed.
        $message = $this->record($enrollment, $step, OutreachMessage::STATUS_SENDING, null, $copy);

        try {
            Mail::to($prospect->email, $prospect->contact_name)
                ->send(new OutreachEmail($prospect, $copy['subject'], $copy['body'], $campaign->goal));

            $message->update([
                'status' => OutreachMessage::STATUS_SENT,
                'error' => null,
                'sent_at' => now(),
            ]);

            $prospect->update(['last_contacted_at' => now()]);

            if ($prospect->status === OutreachProspect::STATUS_NEW) {
                $prospect->update(['status' => OutreachProspect::STATUS_CONTACTED]);
            }

            $this->advance($enrollment, $campaign, $step);
        } catch (Throwable $e) {
            report($e);

            $message->update([
                'status' => OutreachMessage::STATUS_FAILED,
                'error' => Str::limit($e->getMessage(), 490),
            ]);

            $this->handleFailure($enrollment);
        }

        return $message->refresh();
    }

    /**
     * Move the person to the next step, or close the sequence.
     */
    private function advance(OutreachEnrollment $enrollment, OutreachCampaign $campaign, OutreachCampaignStep $sent): void
    {
        $nextPosition = $sent->position + 1;
        $next = $campaign->stepAt($nextPosition);

        if ($next === null) {
            $enrollment->stop(OutreachEnrollment::STATUS_FINISHED, 'Sequence complete — every step has been sent.');

            return;
        }

        $enrollment->update([
            'current_step' => $nextPosition,
            'next_send_at' => now()->addDays($next->delay_days),
        ]);
    }

    /**
     * A failed send is usually a configuration problem, and a configuration
     * problem fails every single message. Retrying once is sensible; retrying
     * forever burns the daily allowance on nothing and hides the fault. So the
     * sequence backs off for an hour, and gives up after three attempts with the
     * reason left where an administrator will see it.
     */
    private function handleFailure(OutreachEnrollment $enrollment): void
    {
        $failures = $enrollment->messages()
            ->where('status', OutreachMessage::STATUS_FAILED)
            ->count();

        if ($failures >= self::FAILURE_LIMIT) {
            $enrollment->stop(
                OutreachEnrollment::STATUS_PAUSED,
                $failures.' sends failed in a row — check the mail configuration, then resume this person.',
            );

            return;
        }

        $enrollment->update(['next_send_at' => now()->addHour()]);
    }

    /**
     * The moment the prospect becomes contactable again, or null if they are
     * contactable now.
     */
    private function cooldownEndsAt(OutreachProspect $prospect): ?Carbon
    {
        $days = (int) config('outreach.cooldown_days');

        if ($days <= 0 || $prospect->last_contacted_at === null) {
            return null;
        }

        $ends = $prospect->last_contacted_at->copy()->addDays($days);

        return $ends->isFuture() ? $ends : null;
    }

    /**
     * @param  array{subject: string, body: string}|null  $copy
     */
    private function record(
        OutreachEnrollment $enrollment,
        OutreachCampaignStep $step,
        string $status,
        ?string $skipReason = null,
        ?array $copy = null,
        ?string $error = null,
    ): OutreachMessage {
        return OutreachMessage::create([
            'outreach_enrollment_id' => $enrollment->getKey(),
            'outreach_prospect_id' => $enrollment->outreach_prospect_id,
            'outreach_campaign_id' => $enrollment->outreach_campaign_id,
            'outreach_campaign_step_id' => $step->getKey(),
            'step_position' => $step->position,
            'to_email' => (string) $enrollment->prospect?->email,
            'subject' => $copy['subject'] ?? $step->subject,
            'body' => $copy['body'] ?? $step->body,
            'transport' => MailDelivery::transport(),
            'status' => $status,
            'error' => $error,
            'skip_reason' => $skipReason,
        ]);
    }
}
