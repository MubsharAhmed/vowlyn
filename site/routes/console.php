<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Cold outreach
|--------------------------------------------------------------------------
|
| Checks every quarter of an hour whether anybody is due a step. That is what
| spreads a day's allowance across the working day instead of firing it in one
| burst — the commands themselves only send what the window and the daily limit
| allow, so a run that finds nothing costs one query.
|
| withoutOverlapping() matters because a run pauses between sends: without it,
| a slow run and the next scheduled one would both work through the same queue.
|
| Needs the scheduler to be running in production — one cron entry, or
| `php artisan schedule:work`. See OUTREACH.md.
|
*/
Schedule::command('outreach:send')
    ->everyFifteenMinutes()
    ->withoutOverlapping();
