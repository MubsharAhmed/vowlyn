<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\OutreachCampaign;
use App\Models\OutreachMessage;
use Illuminate\Support\Carbon;

/**
 * How many messages may still leave today.
 *
 * The limit is global rather than per campaign because the thing being
 * protected is the sending domain's reputation, and a mailbox does not care
 * which campaign a message came from. Campaigns may ask for a smaller slice,
 * never a larger one.
 */
final class OutreachQuota
{
    public static function dailyLimit(): int
    {
        return max(1, (int) config('outreach.daily_limit'));
    }

    public static function campaignLimit(?OutreachCampaign $campaign): int
    {
        $override = $campaign?->daily_limit;

        if ($override === null || $override <= 0) {
            return self::dailyLimit();
        }

        return min($override, self::dailyLimit());
    }

    /**
     * Messages genuinely handed to the mailer today, in the outreach timezone.
     * Refusals and failures do not count against the allowance — the point is
     * how much mail left the building.
     */
    public static function sentToday(?OutreachCampaign $campaign = null): int
    {
        // Compare UTC-stored timestamps with the start of the business day,
        // including the one-hour offset during daylight saving time.
        $since = Carbon::now(OutreachWindow::timezone())->startOfDay()->setTimezone(config('app.timezone', 'UTC'));

        $query = OutreachMessage::query()
            ->where('status', OutreachMessage::STATUS_SENT)
            ->where('sent_at', '>=', $since);

        if ($campaign !== null) {
            $query->where('outreach_campaign_id', $campaign->getKey());
        }

        return $query->count();
    }

    public static function remainingToday(?OutreachCampaign $campaign = null): int
    {
        $globalRemaining = max(0, self::dailyLimit() - self::sentToday());

        if ($campaign === null) {
            return $globalRemaining;
        }

        $campaignRemaining = max(0, self::campaignLimit($campaign) - self::sentToday($campaign));

        return min($globalRemaining, $campaignRemaining);
    }

    public static function exhausted(?OutreachCampaign $campaign = null): bool
    {
        return self::remainingToday($campaign) === 0;
    }

    /**
     * How many more may go out right now: today's allowance, further capped by
     * the requested run size.
     */
    public static function runAllowance(int $wanted, ?OutreachCampaign $campaign = null): int
    {
        return max(0, min($wanted, self::remainingToday($campaign)));
    }

    public static function summary(?OutreachCampaign $campaign = null): string
    {
        $limit = $campaign === null ? self::dailyLimit() : self::campaignLimit($campaign);

        return self::sentToday($campaign).' of '.$limit.' sent today · '.self::remainingToday($campaign).' left';
    }
}
