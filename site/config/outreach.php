<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Cold outreach
|--------------------------------------------------------------------------
|
| Outreach is email to people who never asked to hear from us. That makes it
| the one part of this application that can damage the studio's reputation if
| it is done carelessly: too much, too fast, to the wrong address, or without a
| way out, and the sending domain gets filtered for everybody — including the
| replies to real enquiries.
|
| Every number here is a guardrail, not a preference. The defaults are
| deliberately conservative: a new domain that suddenly sends 500 messages a
| day is filtered on the first day. Raise the daily limit gradually, and only
| once the sending domain has some history.
|
*/

return [

    /*
    | How many outreach messages may leave the building in a single day, across
    | every campaign. This is the hard ceiling — campaigns can ask for less.
    */
    'daily_limit' => (int) env('OUTREACH_DAILY_LIMIT', 40),

    /*
    | The most messages one run of the sender will dispatch. Keeps a single
    | scheduled run (or one click of "send due now") bounded and predictable.
    */
    'per_run' => (int) env('OUTREACH_PER_RUN', 20),

    /*
    | Seconds to wait between individual sends inside a run. Sending forty
    | messages in one burst is a pattern receiving servers notice.
    */
    'interval_seconds' => (int) env('OUTREACH_INTERVAL_SECONDS', 3),

    /*
    | Days before the same person may be approached again by any campaign.
    | Without this, two campaigns with overlapping lists double-email the same
    | prospect within a week, which is the fastest way to collect a complaint.
    */
    'cooldown_days' => (int) env('OUTREACH_COOLDOWN_DAYS', 30),

    /*
    | The sending window, in the timezone below. Outreach that arrives at 3am
    | reads as automated; the same message at 9:30am reads as a person.
    */
    'window' => [
        'start' => (string) env('OUTREACH_WINDOW_START', '09:00'),
        'end' => (string) env('OUTREACH_WINDOW_END', '17:00'),
        'weekdays_only' => (bool) env('OUTREACH_WEEKDAYS_ONLY', true),
    ],

    /*
    | The clock the window is measured against. Set this to the business
    | timezone, not the server's — a window in UTC is a window in the wrong
    | place as soon as the clocks change.
    */
    'timezone' => env('OUTREACH_TIMEZONE', config('app.timezone', 'UTC')),

    /*
    | Appended to every outreach email. A physical postal address and a working
    | opt-out are required by CAN-SPAM and expected under GDPR/PECR, and their
    | absence is a common reason a cold email is filtered on sight.
    */
    'footer' => [
        'postal_address' => (string) env('OUTREACH_POSTAL_ADDRESS', ''),
    ],

    /*
    | Import guardrails. A spreadsheet is the usual way a list arrives, and a
    | truncated or malformed file should be rejected rather than half-imported.
    */
    'import' => [
        'max_rows' => (int) env('OUTREACH_IMPORT_MAX_ROWS', 2000),
    ],

];
