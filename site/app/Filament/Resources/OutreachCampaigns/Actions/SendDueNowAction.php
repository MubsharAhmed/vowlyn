<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachCampaigns\Actions;

use App\Models\OutreachCampaign;
use App\Services\OutreachDispatcher;
use App\Support\MailDelivery;
use App\Support\OutreachQuota;
use App\Support\OutreachWindow;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

/**
 * Runs the sender on demand, after showing what it is about to do.
 *
 * The confirmation is not ceremony. Outreach is the one thing in this
 * application that reaches strangers, so the person clicking should see the
 * three numbers that decide the outcome — what is due, what the day's allowance
 * has left, and how many will really go in this run — before anything leaves the
 * building.
 *
 * The same action serves a campaign row (that campaign) and the campaign list
 * header (everything active), decided by whether Filament hands it a record.
 */
final class SendDueNowAction
{
    public static function make(): Action
    {
        return Action::make('sendDueNow')
            ->label(fn (?OutreachCampaign $record = null): string => $record === null ? 'Send what is due' : 'Send now')
            ->icon(Heroicon::OutlinedPaperAirplane)
            ->requiresConfirmation()
            ->modalHeading('Send the messages that are due')
            ->modalDescription(fn (?OutreachCampaign $record = null): Htmlable => new HtmlString(self::preview($record)))
            ->modalSubmitActionLabel('Send now')
            ->action(function (?OutreachCampaign $record = null): void {
                $result = app(OutreachDispatcher::class)->run(campaign: $record);

                $notification = Notification::make()
                    ->title(match (true) {
                        $result['failed'] > 0 => 'Send failed',
                        $result['sent'] > 0 && ! MailDelivery::isLive() => 'Logged '.$result['sent'].' preview message(s)',
                        $result['sent'] > 0 => 'Sent '.$result['sent'].' message(s)',
                        default => 'Nothing was sent',
                    })
                    ->body($result['message']);

                match (true) {
                    $result['failed'] > 0 => $notification->danger(),
                    $result['sent'] > 0 && MailDelivery::isLive() => $notification->success(),
                    default => $notification->warning(),
                };

                $notification->send();
            });
    }

    private static function preview(?OutreachCampaign $campaign): string
    {
        $due = app(OutreachDispatcher::class)->dueCount($campaign);
        $closure = OutreachWindow::closure();
        $allowance = OutreachQuota::remainingToday($campaign);

        $warnings = [];

        if (! MailDelivery::isLive()) {
            $warnings[] = '<strong style="color:#b91c1c;">Email is not configured ('.e(MailDelivery::transport()).').</strong> '
                .'Messages will be recorded in the log and marked as not delivered.';
        }

        if ($closure !== null) {
            $warnings[] = 'The sending window is shut ('.e($closure).'). '.e(OutreachWindow::nextOpenLabel())
                .' — the work is held over, not dropped.';
        }

        if ($allowance === 0) {
            $warnings[] = 'Today\'s sending allowance is used up. '.e(OutreachWindow::nextOpenLabel()).'.';
        }

        $html = '<ul style="margin:0;padding-left:18px;line-height:1.8;">';

        foreach ($warnings as $warning) {
            $html .= '<li>'.$warning.'</li>';
        }

        $html .= '<li>'.e(
            $due.' message(s) are due right now'
            .($campaign === null ? ' across every active campaign' : ' in this campaign').'.',
        ).'</li>';

        $html .= '<li>'.e(
            'Today\'s allowance: '.OutreachQuota::summary($campaign)
            .'. This run sends at most '.min((int) config('outreach.per_run'), max(0, $allowance)).'.',
        ).'</li>';

        return $html.'</ul>';
    }
}
