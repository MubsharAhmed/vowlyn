<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmailSuppression;
use App\Models\OutreachCampaign;
use App\Models\OutreachEnrollment;
use App\Models\OutreachMessage;
use App\Models\OutreachProspect;
use App\Support\MailDelivery;
use App\Support\OutreachQuota;
use App\Support\OutreachWindow;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * Decides what goes out, in what order, and what has to wait.
 *
 * The sender knows how to deliver one message; this class knows when delivering
 * is a bad idea. Three gates stand in front of every run — the sending window,
 * the daily allowance, and the per-run ceiling — and the winning constraint is
 * always the most conservative one. A run that is refused defers the work to the
 * next open window rather than dropping it, so a queue that is held overnight
 * still arrives in the morning.
 */
final class OutreachDispatcher
{
    public function __construct(private readonly OutreachSender $sender) {}

    /**
     * Send whatever is due right now, and report exactly what happened.
     *
     * @return array{sent: int, skipped: int, failed: int, considered: int, deferred: int, ran: bool, message: string}
     */
    public function run(?int $limit = null, ?OutreachCampaign $campaign = null): array
    {
        $perRunLimit = max(1, (int) config('outreach.per_run'));
        $limit = min($perRunLimit, max(1, $limit ?? $perRunLimit));

        if (! OutreachWindow::isOpen()) {
            $deferred = $this->deferDue(OutreachWindow::nextOpen(), $campaign);

            return $this->result(
                sent: 0,
                skipped: 0,
                failed: 0,
                considered: 0,
                deferred: $deferred,
                message: sprintf(
                    'Held: outside the sending window (%s). %s.%s',
                    OutreachWindow::closure(),
                    OutreachWindow::nextOpenLabel(),
                    $deferred > 0 ? " {$deferred} message(s) held over." : '',
                ),
            );
        }

        $allowance = OutreachQuota::runAllowance($limit, $campaign);

        if ($allowance === 0) {
            $deferred = $this->deferDue(OutreachWindow::nextOpen(), $campaign);

            return $this->result(
                sent: 0,
                skipped: 0,
                failed: 0,
                considered: 0,
                deferred: $deferred,
                message: 'Held: the daily sending limit is used up. '.OutreachWindow::nextOpenLabel().'.',
            );
        }

        $enrollments = $this->dueQuery($campaign)->limit($allowance)->get();

        if ($enrollments->isEmpty()) {
            return $this->result(
                sent: 0,
                skipped: 0,
                failed: 0,
                considered: 0,
                deferred: 0,
                message: 'Nothing due — no one is waiting on a step right now.',
            );
        }

        $sent = 0;
        $skipped = 0;
        $failed = 0;
        $total = $enrollments->count();
        $interval = (int) config('outreach.interval_seconds');

        foreach ($enrollments->values() as $index => $enrollment) {
            $message = $this->sender->send($enrollment);

            match ($message?->status) {
                OutreachMessage::STATUS_SENT => $sent++,
                OutreachMessage::STATUS_SKIPPED => $skipped++,
                OutreachMessage::STATUS_FAILED => $failed++,
                default => null,
            };

            // Space the sends out. Skipped entirely when no message is really
            // being delivered, so a development machine on the log transport is
            // not made to wait for nothing.
            if ($interval > 0 && $sent > 0 && MailDelivery::isLive() && $index < $total - 1) {
                sleep($interval);
            }
        }

        $message = sprintf(
            MailDelivery::isLive() ? 'Sent %d of %d. %s' : 'Logged only %d of %d; no email was delivered. %s',
            $sent,
            $total,
            trim(sprintf(
                '%s%s%s',
                $skipped > 0 ? "{$skipped} held back. " : '',
                $failed > 0 ? "{$failed} failed. " : '',
                OutreachQuota::summary($campaign),
            )),
        );

        return $this->result($sent, $skipped, $failed, $total, 0, $message);
    }

    /**
     * Everybody whose next step is due, in the order they have been waiting.
     *
     * @return Builder<OutreachEnrollment>
     */
    public function dueQuery(?OutreachCampaign $campaign = null): Builder
    {
        $query = OutreachEnrollment::query()
            ->due()
            ->whereHas('campaign', fn (Builder $inner) => $inner->sending())
            ->whereHas('prospect')
            ->orderBy('next_send_at')
            ->orderBy('id');

        if ($campaign !== null) {
            $query->where('outreach_campaign_id', $campaign->getKey());
        }

        return $query;
    }

    public function dueCount(?OutreachCampaign $campaign = null): int
    {
        return $this->dueQuery($campaign)->count();
    }

    /**
     * Hold every due enrollment until a given moment.
     */
    public function deferDue(CarbonInterface $until, ?OutreachCampaign $campaign = null): int
    {
        return $this->dueQuery($campaign)->update(['next_send_at' => $until]);
    }

    /**
     * Why this prospect should not be added to this campaign, or null when they
     * can be. Returned as a sentence so the panel can explain itself.
     */
    public function canEnroll(OutreachProspect $prospect, OutreachCampaign $campaign): ?string
    {
        if ($prospect->isSuppressed()) {
            return 'On the opt-out list.';
        }

        if ($prospect->blockedFromContact()) {
            return 'Marked “'.$prospect->statusLabel().'”.';
        }

        if ($campaign->enrollments()->where('outreach_prospect_id', $prospect->getKey())->exists()) {
            return 'Already in this campaign.';
        }

        if ($campaign->steps()->count() === 0) {
            return 'This campaign has no steps yet.';
        }

        return null;
    }

    public function enroll(OutreachProspect $prospect, OutreachCampaign $campaign): ?OutreachEnrollment
    {
        if ($this->canEnroll($prospect, $campaign) !== null) {
            return null;
        }

        $first = $campaign->steps()->first();

        return OutreachEnrollment::create([
            'outreach_campaign_id' => $campaign->getKey(),
            'outreach_prospect_id' => $prospect->getKey(),
            'current_step' => $first?->position ?? 1,
            'status' => OutreachEnrollment::STATUS_ACTIVE,
            // A first step may itself be delayed — for instance when the studio
            // wants a waiting period after the list is loaded.
            'next_send_at' => now()->addDays($first?->delay_days ?? 0),
            'enrolled_at' => now(),
        ]);
    }

    /**
     * Enrol a batch, keeping a reason for each refusal.
     *
     * @param  iterable<OutreachProspect>  $prospects
     * @return array{enrolled: int, skipped: array<string, string>}
     */
    public function enrollMany(iterable $prospects, OutreachCampaign $campaign): array
    {
        $enrolled = 0;
        $skipped = [];

        foreach ($prospects as $prospect) {
            $reason = $this->canEnroll($prospect, $campaign);

            if ($reason !== null) {
                $skipped[$prospect->email] = $reason;

                continue;
            }

            $this->enroll($prospect, $campaign);
            $enrolled++;
        }

        return ['enrolled' => $enrolled, 'skipped' => $skipped];
    }

    /**
     * End every active sequence for one person.
     *
     * Called when somebody replies, bounces, asks to stop or is marked not
     * interested. This is the rule that separates a tool from a nuisance: a
     * person who has answered must never receive the follow-up that was already
     * scheduled for Thursday.
     */
    public function stopAllFor(OutreachProspect $prospect, string $status, string $reason): int
    {
        $stopped = 0;

        $enrollments = $prospect->enrollments()
            ->whereIn('status', [OutreachEnrollment::STATUS_ACTIVE, OutreachEnrollment::STATUS_PAUSED])
            ->get();

        foreach ($enrollments as $enrollment) {
            $enrollment->stop($status, $reason);
            $stopped++;
        }

        return $stopped;
    }

    /**
     * Record an opt-out and end every sequence for that address.
     */
    public function unsubscribe(OutreachProspect $prospect, string $reason = EmailSuppression::REASON_UNSUBSCRIBED, ?string $note = null): EmailSuppression
    {
        $suppression = EmailSuppression::add($prospect->email, $reason, $note, 'opt-out link');

        $this->stopAllFor($prospect, OutreachEnrollment::STATUS_UNSUBSCRIBED, 'Asked not to be contacted again.');

        if ($prospect->status !== OutreachProspect::STATUS_UNSUBSCRIBED) {
            $prospect->update(['status' => OutreachProspect::STATUS_UNSUBSCRIBED]);
        }

        return $suppression;
    }

    /**
     * @return array{sent: int, skipped: int, failed: int, considered: int, deferred: int, ran: bool, message: string}
     */
    private function result(int $sent, int $skipped, int $failed, int $considered, int $deferred, string $message): array
    {
        return [
            'sent' => $sent,
            'skipped' => $skipped,
            'failed' => $failed,
            'considered' => $considered,
            'deferred' => $deferred,
            'ran' => $sent > 0 || $failed > 0 || $skipped > 0 || $deferred > 0,
            'message' => $message,
        ];
    }
}
