<?php

declare(strict_types=1);

namespace App\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * When outreach is allowed to go out.
 *
 * Cold email that arrives at 3am reads as automated, and the same message at
 * 9:30am reads as a person who happened to be thinking about you. The window is
 * also a rate limiter in disguise: twenty messages spread across an eight-hour
 * day look nothing like twenty in one burst.
 */
final class OutreachWindow
{
    public static function timezone(): string
    {
        return (string) config('outreach.timezone');
    }

    public static function now(): CarbonImmutable
    {
        return CarbonImmutable::now(self::timezone());
    }

    public static function isOpen(?CarbonInterface $at = null): bool
    {
        return self::closure($at) === null;
    }

    /**
     * Why the window is shut, or null when it is open.
     */
    public static function closure(?CarbonInterface $at = null): ?string
    {
        $moment = self::localise($at);

        if ((bool) config('outreach.window.weekdays_only') && $moment->isWeekend()) {
            return 'the weekend';
        }

        $minutes = $moment->hour * 60 + $moment->minute;
        $start = self::minutes((string) config('outreach.window.start'));
        $end = self::minutes((string) config('outreach.window.end'));

        if ($minutes < $start) {
            return sprintf('before %s', self::label($start));
        }

        if ($minutes >= $end) {
            return sprintf('after %s', self::label($end));
        }

        return null;
    }

    /**
     * The next moment the window is open, so a closed window delays a send
     * rather than dropping it.
     */
    public static function nextOpen(?CarbonInterface $at = null): CarbonImmutable
    {
        $moment = self::localise($at);
        $start = self::minutes((string) config('outreach.window.start'));

        // A week of looking is enough for any combination of hours and weekend
        // settings; the loop is bounded so a misconfiguration cannot hang.
        for ($day = 0; $day <= 7; $day++) {
            $candidate = $moment->startOfDay()->addDays($day)->addMinutes($start);

            if ((bool) config('outreach.window.weekdays_only') && $candidate->isWeekend()) {
                continue;
            }

            if ($candidate->greaterThan($moment)) {
                return $candidate;
            }
        }

        return $moment->addDay()->startOfDay()->addMinutes($start);
    }

    /**
     * A short sentence for the panel, e.g. "Sends resume Monday 09:00".
     */
    public static function nextOpenLabel(?CarbonInterface $at = null): string
    {
        return 'Sends resume '.self::nextOpen($at)->format('D j M, H:i');
    }

    private static function localise(?CarbonInterface $at): CarbonImmutable
    {
        return $at === null
            ? self::now()
            : CarbonImmutable::instance($at)->setTimezone(self::timezone());
    }

    private static function minutes(string $time): int
    {
        [$hours, $minutes] = array_pad(explode(':', $time, 2), 2, '0');

        return max(0, min(1439, ((int) $hours * 60) + (int) $minutes));
    }

    private static function label(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}
