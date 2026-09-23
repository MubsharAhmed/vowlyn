<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\EmailSuppression;
use App\Models\OutreachCampaign;
use App\Models\OutreachEnrollment;
use App\Models\OutreachMessage;
use App\Services\OutreachDispatcher;
use App\Support\MailDelivery;
use App\Support\OutreachQuota;
use App\Support\OutreachWindow;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * The state of outreach, on the screen the studio sees when it logs in.
 *
 * The point of putting it here rather than on an outreach page is that the two
 * things worth knowing are both quiet failures: mail that is not configured, and
 * a window that is shut so a campaign looks like it is not working. Neither
 * raises an error, and both are invisible unless something says so.
 */
final class OutreachOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Outreach';

    protected function getStats(): array
    {
        $stats = [];

        if (! MailDelivery::isLive()) {
            $stats[] = Stat::make('Email is not configured', 'Not delivering')
                ->description('Outreach would be written to the log and marked as not delivered. Add the mail credentials to .env — see MAIL-REPLIES.md.')
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle)
                ->color('danger')
                ->icon(Heroicon::OutlinedEnvelope);
        }

        $sent = OutreachMessage::query()
            ->where('status', OutreachMessage::STATUS_SENT)
            ->where('sent_at', '>=', now()->subDays(7))
            ->count();

        $active = OutreachEnrollment::query()
            ->where('status', OutreachEnrollment::STATUS_ACTIVE)
            ->whereHas('campaign', fn ($query) => $query->sending())
            ->count();

        $stats[] = Stat::make('Sent in the last 7 days', (string) $sent)
            ->description($active.' person(s) waiting on a step')
            ->descriptionIcon(Heroicon::OutlinedClock)
            ->color('info')
            ->icon(Heroicon::OutlinedPaperAirplane);

        $replies = OutreachEnrollment::query()
            ->whereIn('status', OutreachCampaign::ENGAGED_STATUSES)
            ->where('replied_at', '>=', now()->subDays(7))
            ->count();

        $interested = OutreachEnrollment::query()
            ->whereIn('status', OutreachCampaign::ENGAGED_STATUSES)
            ->count();

        $stats[] = Stat::make('Replies in the last 7 days', (string) $replies)
            ->description($interested.' interested in total')
            ->descriptionIcon(Heroicon::OutlinedChatBubbleLeftEllipsis)
            ->color($replies > 0 ? 'success' : 'gray')
            ->icon(Heroicon::OutlinedChatBubbleLeftEllipsis);

        $due = app(OutreachDispatcher::class)->dueCount();
        $closure = OutreachWindow::closure();

        $stats[] = Stat::make('Ready to send', (string) $due)
            ->description($closure === null
                ? 'Window open until '.config('outreach.window.end')
                : 'Window shut ('.$closure.') — '.OutreachWindow::nextOpenLabel())
            ->descriptionIcon($closure === null ? Heroicon::OutlinedCheckCircle : Heroicon::OutlinedClock)
            ->color($closure === null ? 'success' : 'warning')
            ->icon(Heroicon::OutlinedInboxArrowDown);

        $stats[] = Stat::make(
            'Today\'s allowance left',
            (string) OutreachQuota::remainingToday(),
        )
            ->description(OutreachQuota::sentToday().' of '.OutreachQuota::dailyLimit().' sent today')
            ->color(OutreachQuota::exhausted() ? 'danger' : 'gray')
            ->icon(Heroicon::OutlinedAdjustmentsHorizontal);

        $stats[] = Stat::make('On the opt-out list', (string) EmailSuppression::query()->count())
            ->description('These addresses are never emailed again')
            ->color('gray')
            ->icon(Heroicon::OutlinedNoSymbol);

        return $stats;
    }
}
