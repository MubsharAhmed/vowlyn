<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Mail\OutreachEmail;
use App\Models\EmailSuppression;
use App\Models\OutreachCampaign;
use App\Models\OutreachCampaignStep;
use App\Models\OutreachEnrollment;
use App\Models\OutreachMessage;
use App\Models\OutreachProspect;
use App\Services\OutreachDispatcher;
use App\Services\OutreachSender;
use App\Services\OutreachListImporter;
use App\Support\OutreachLint;
use App\Support\OutreachQuota;
use App\Support\OutreachWindow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

final class OutreachTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // A window that is always open, no waiting between sends, and mail that
        // actually delivers — each test then narrows exactly one of these.
        config([
            'outreach.window.start' => '00:00',
            'outreach.window.end' => '23:59',
            'outreach.window.weekdays_only' => false,
            'outreach.interval_seconds' => 0,
            'outreach.timezone' => 'UTC',
            'outreach.daily_limit' => 40,
            'outreach.per_run' => 20,
            'outreach.cooldown_days' => 30,
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.example.com',
            'mail.mailers.smtp.username' => 'studio@example.com',
            'mail.reply_to.address' => 'hello@example.com',
            'mail.reply_to.name' => 'Vowlyn',
            'outreach.footer.postal_address' => '1 Test Street, London',
        ]);
    }

    /**
     * @param  array<int, array{delay_days?: int, subject?: string, body?: string}>  $steps
     */
    private function campaign(string $status = OutreachCampaign::STATUS_ACTIVE, array $steps = []): OutreachCampaign
    {
        $campaign = OutreachCampaign::create([
            'name' => 'Audit sequence',
            'goal' => 'a free website audit',
            'status' => $status,
        ]);

        $steps = $steps === [] ? [
            ['delay_days' => 0, 'subject' => 'A note about {company}', 'body' => "Hi {first_name},\n\nWould {company} in {region} like a free website audit?\n"],
            ['delay_days' => 4, 'subject' => 'Following up, {first_name}', 'body' => "Hi {first_name},\n\nJust checking in once.\n"],
            ['delay_days' => 10, 'subject' => 'Closing the loop', 'body' => "Hi {first_name},\n\nI will stop here.\n"],
        ] : $steps;

        foreach ($steps as $index => $step) {
            $campaign->steps()->create([
                'position' => $index + 1,
                'delay_days' => $step['delay_days'] ?? 0,
                'subject' => $step['subject'] ?? 'Step '.($index + 1),
                'body' => $step['body'] ?? 'Body '.($index + 1),
            ]);
        }

        return $campaign->refresh();
    }

    private function prospect(array $attributes = []): OutreachProspect
    {
        return OutreachProspect::create(array_replace([
            'company' => 'Halstead & Rowe',
            'contact_name' => 'Priya Halstead',
            'email' => 'priya@halstead.example.com',
            'role' => 'Director',
            'industry' => 'Accountancy',
            'region' => 'Manchester',
            'status' => OutreachProspect::STATUS_NEW,
        ], $attributes));
    }

    private function enroll(OutreachCampaign $campaign, OutreachProspect $prospect): ?OutreachEnrollment
    {
        return app(OutreachDispatcher::class)->enroll($prospect, $campaign);
    }

    public function test_scheduler_does_not_advance_people_when_mail_only_writes_to_the_log(): void
    {
        Mail::fake();
        config(['mail.default' => 'log']);

        $campaign = $this->campaign();
        $enrollment = $this->enroll($campaign, $this->prospect());

        $this->artisan('outreach:send')->assertExitCode(1);

        Mail::assertNothingSent();
        $this->assertDatabaseCount('outreach_messages', 0);
        $this->assertSame(1, $enrollment->refresh()->current_step);
    }

    public function test_a_draft_campaign_sends_nothing(): void
    {
        Mail::fake();

        $campaign = $this->campaign(OutreachCampaign::STATUS_DRAFT);
        $this->enroll($campaign, $this->prospect());

        $result = app(OutreachDispatcher::class)->run();

        $this->assertSame(0, $result['sent']);
        Mail::assertNothingSent();
        $this->assertDatabaseCount('outreach_messages', 0);
    }

    public function test_an_active_campaign_sends_the_first_step_and_advances(): void
    {
        Mail::fake();

        $campaign = $this->campaign();
        $prospect = $this->prospect();
        $enrollment = $this->enroll($campaign, $prospect);

        $result = app(OutreachDispatcher::class)->run();

        $this->assertSame(1, $result['sent']);
        $this->assertSame(0, $result['failed']);

        Mail::assertSent(OutreachEmail::class, 1);

        $message = OutreachMessage::query()->latest('id')->first();

        $this->assertNotNull($message);
        $this->assertSame(OutreachMessage::STATUS_SENT, $message->status);
        $this->assertSame(1, $message->step_position);

        // Personalisation is substituted before the message is handed over.
        $this->assertSame('A note about Halstead & Rowe', $message->subject);
        $this->assertStringContainsString('Hi Priya,', $message->body);
        $this->assertStringContainsString('Manchester', $message->body);

        $enrollment->refresh();
        $this->assertSame(2, $enrollment->current_step);
        $this->assertTrue($enrollment->next_send_at->isSameDay(now()->addDays(4)));

        $prospect->refresh();
        $this->assertNotNull($prospect->last_contacted_at);
        $this->assertSame(OutreachProspect::STATUS_CONTACTED, $prospect->status);
    }

    public function test_the_email_carries_personalisation_and_a_working_one_click_opt_out(): void
    {
        $campaign = $this->campaign();
        $prospect = $this->prospect();

        $mailable = new OutreachEmail($prospect, 'A note about {company}', 'Hi {first_name}, hello.', 'a free website audit');

        $headers = $mailable->headers();
        $unsubscribe = route('outreach.unsubscribe', ['token' => $prospect->unsubscribe_token]);

        $this->assertSame('<'.$unsubscribe.'>', $headers->text['List-Unsubscribe']);
        $this->assertSame('List-Unsubscribe=One-Click', $headers->text['List-Unsubscribe-Post']);

        // The text part is not a formality: it is read by filters and by
        // text-only clients, so it has to carry the same opt-out.
        $mailable->assertSeeInText($unsubscribe);
        $mailable->assertSeeInHtml($unsubscribe);
        $mailable->assertSeeInText('1 Test Street, London');
        $mailable->assertSeeInText('a free website audit');

        // Rendered with real tokens, the copy reads as a message to a person.
        $step = $campaign->steps()->first();
        $rendered = $step->renderFor($prospect);

        $this->assertSame('A note about Halstead & Rowe', $rendered['subject']);
        $this->assertStringContainsString('Hi Priya', $rendered['body']);
    }

    public function test_an_address_on_the_opt_out_list_is_never_emailed(): void
    {
        Mail::fake();

        $campaign = $this->campaign();
        $prospect = $this->prospect();
        $enrollment = $this->enroll($campaign, $prospect);

        EmailSuppression::add($prospect->email, EmailSuppression::REASON_ASKED, 'Phoned us', 'admin panel');

        app(OutreachDispatcher::class)->run();

        Mail::assertNothingSent();

        $enrollment->refresh();
        $this->assertSame(OutreachEnrollment::STATUS_UNSUBSCRIBED, $enrollment->status);
        $this->assertStringContainsString('opt-out list', (string) $enrollment->stop_reason);

        $message = OutreachMessage::query()->latest('id')->first();
        $this->assertNotNull($message);
        $this->assertSame(OutreachMessage::STATUS_SKIPPED, $message->status);
        $this->assertNotNull($message->skip_reason);
    }

    public function test_somebody_marked_not_interested_is_skipped_and_stopped(): void
    {
        Mail::fake();

        $campaign = $this->campaign();
        $prospect = $this->prospect();
        $enrollment = $this->enroll($campaign, $prospect);

        // Enrolled while reachable, then marked not interested — the realistic
        // order, because somebody who is already blocking us cannot be enrolled.
        $prospect->update(['status' => OutreachProspect::STATUS_NOT_INTERESTED]);

        app(OutreachDispatcher::class)->run();

        Mail::assertNothingSent();
        $this->assertNotNull($enrollment);
        $this->assertSame(OutreachEnrollment::STATUS_NOT_INTERESTED, $enrollment->refresh()->status);

        $message = OutreachMessage::query()->latest('id')->first();
        $this->assertSame(OutreachMessage::STATUS_SKIPPED, $message?->status);
    }

    public function test_the_daily_limit_stops_a_batch_part_way_through(): void
    {
        Mail::fake();
        config(['outreach.daily_limit' => 1]);

        $campaign = $this->campaign();
        $first = $this->enroll($campaign, $this->prospect(['email' => 'one@example.com', 'contact_name' => 'One']));
        $second = $this->enroll($campaign, $this->prospect(['email' => 'two@example.com', 'contact_name' => 'Two']));

        $result = app(OutreachDispatcher::class)->run();

        $this->assertSame(1, $result['sent']);
        Mail::assertSent(OutreachEmail::class, 1);
        $this->assertSame(1, OutreachQuota::sentToday());
        $this->assertSame(0, OutreachQuota::remainingToday());

        // The second person is still waiting, not lost: their step stays due.
        $this->assertSame(2, $first->refresh()->current_step);
        $this->assertSame(1, $second->refresh()->current_step);
        $this->assertSame(OutreachEnrollment::STATUS_ACTIVE, $second->status);

        // And the next run, still over quota, holds the work rather than sending.
        $held = app(OutreachDispatcher::class)->run();
        $this->assertSame(0, $held['sent']);
        $this->assertSame(1, $held['deferred']);
        Mail::assertSent(OutreachEmail::class, 1);
    }

    public function test_a_campaign_specific_run_cannot_exceed_the_site_wide_daily_limit(): void
    {
        Mail::fake();
        config(['outreach.daily_limit' => 1]);

        $firstCampaign = $this->campaign();
        $secondCampaign = $this->campaign();
        $this->enroll($firstCampaign, $this->prospect(['email' => 'one@example.com']));
        $secondEnrollment = $this->enroll($secondCampaign, $this->prospect(['email' => 'two@example.com']));

        $this->assertSame(1, app(OutreachDispatcher::class)->run(campaign: $firstCampaign)['sent']);
        $this->assertSame(0, OutreachQuota::remainingToday($secondCampaign));
        $this->assertSame(0, app(OutreachDispatcher::class)->run(campaign: $secondCampaign)['sent']);
        $this->assertSame(1, $secondEnrollment->refresh()->current_step);
        Mail::assertSent(OutreachEmail::class, 1);
    }

    public function test_daily_quota_uses_the_business_day_across_daylight_saving_time(): void
    {
        Mail::fake();
        config(['outreach.timezone' => 'Europe/London', 'outreach.daily_limit' => 1]);
        Carbon::setTestNow('2026-07-01 23:30:00 UTC');

        try {
            $campaign = $this->campaign();
            $enrollment = $this->enroll($campaign, $this->prospect());

            $this->assertSame(OutreachMessage::STATUS_SENT, app(OutreachSender::class)->send($enrollment)?->status);
            $this->assertSame(1, OutreachQuota::sentToday());
            $this->assertSame(0, OutreachQuota::remainingToday());
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_a_single_person_send_obeys_the_window_and_daily_limit(): void
    {
        Mail::fake();
        $campaign = $this->campaign();
        $first = $this->enroll($campaign, $this->prospect(['email' => 'one@example.com']));
        $second = $this->enroll($campaign, $this->prospect(['email' => 'two@example.com']));
        $sender = app(OutreachSender::class);

        Carbon::setTestNow('2026-09-26 12:00:00');
        config(['outreach.window.weekdays_only' => true]);

        try {
            $held = $sender->send($first);
            $this->assertSame(OutreachMessage::STATUS_SKIPPED, $held?->status);
            $this->assertStringContainsString('window is closed', (string) $held?->skip_reason);
            Mail::assertNothingSent();
        } finally {
            Carbon::setTestNow();
        }

        config(['outreach.window.weekdays_only' => false, 'outreach.daily_limit' => 1]);
        $this->assertSame(OutreachMessage::STATUS_SENT, $sender->send($first)?->status);
        $limited = $sender->send($second);
        $this->assertSame(OutreachMessage::STATUS_SKIPPED, $limited?->status);
        $this->assertStringContainsString('daily sending limit', (string) $limited?->skip_reason);
        Mail::assertSent(OutreachEmail::class, 1);
    }

    public function test_the_sending_window_holds_work_until_it_reopens(): void
    {
        Mail::fake();
        config([
            'outreach.window.weekdays_only' => true,
            'outreach.window.start' => '09:00',
            'outreach.window.end' => '17:00',
        ]);

        $campaign = $this->campaign();
        $enrollment = $this->enroll($campaign, $this->prospect());

        // A Saturday afternoon: nobody should receive cold email now.
        $saturday = Carbon::parse('next saturday 14:00', 'UTC');
        $this->travelTo($saturday);

        $result = app(OutreachDispatcher::class)->run();

        $this->assertSame(0, $result['sent']);
        $this->assertSame(1, $result['deferred']);
        Mail::assertNothingSent();

        $enrollment->refresh();
        $this->assertTrue($enrollment->next_send_at->isFuture());
        $this->assertTrue($enrollment->next_send_at->isMonday());
        $this->assertSame('09:00', $enrollment->next_send_at->format('H:i'));

        // And on Monday at nine, it goes.
        $this->travelTo($enrollment->next_send_at->copy()->addMinute());
        $this->assertSame(1, app(OutreachDispatcher::class)->run()['sent']);
    }

    public function test_the_cooldown_delays_a_second_campaign_instead_of_double_emailing(): void
    {
        Mail::fake();

        $prospect = $this->prospect();

        $first = $this->enroll($this->campaign(), $prospect);

        // A second campaign reaching the same person on the same day: allowed to
        // exist, but it must not email them again this month.
        $second = $this->enroll($this->campaign(), $prospect);

        $result = app(OutreachDispatcher::class)->run();

        $this->assertSame(1, $result['sent']);
        Mail::assertSent(OutreachEmail::class, 1);

        $sent = OutreachMessage::query()->where('status', OutreachMessage::STATUS_SENT)->first();
        $this->assertNotNull($sent);
        $this->assertSame($first->getKey(), $sent->outreach_enrollment_id);

        $skipped = OutreachMessage::query()->where('status', OutreachMessage::STATUS_SKIPPED)->first();
        $this->assertNotNull($skipped);
        $this->assertSame($second->getKey(), $skipped->outreach_enrollment_id);
        $this->assertStringContainsString('cooldown', (string) $skipped->skip_reason);

        $second->refresh();
        $this->assertSame(OutreachEnrollment::STATUS_ACTIVE, $second->status);
        $this->assertSame(30, (int) round(now()->diffInDays($second->next_send_at)));
    }

    public function test_a_reply_stops_every_sequence_for_that_person(): void
    {
        Mail::fake();

        $prospect = $this->prospect();
        $first = $this->enroll($this->campaign(), $prospect);
        $second = $this->enroll($this->campaign(), $prospect);

        $stopped = app(OutreachDispatcher::class)->stopAllFor(
            $prospect,
            OutreachEnrollment::STATUS_REPLIED,
            'They replied.',
        );

        $this->assertSame(2, $stopped);
        $this->assertSame(OutreachEnrollment::STATUS_REPLIED, $first->refresh()->status);
        $this->assertSame(OutreachEnrollment::STATUS_REPLIED, $second->refresh()->status);
        $this->assertNotNull($first->refresh()->replied_at);

        // Nothing scheduled survives a reply.
        $this->assertSame(0, app(OutreachDispatcher::class)->dueCount());
    }

    public function test_a_failed_send_is_recorded_and_the_sequence_backs_off(): void
    {
        // A mailer that cannot connect: the failure path has to record what went
        // wrong rather than losing the send silently.
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => '127.0.0.1',
            'mail.mailers.smtp.port' => 1,
            'mail.mailers.smtp.username' => 'studio@example.com',
            'mail.mailers.smtp.timeout' => 1,
        ]);

        $campaign = $this->campaign();
        $enrollment = $this->enroll($campaign, $this->prospect());

        $result = app(OutreachDispatcher::class)->run();

        $this->assertSame(1, $result['failed']);

        $message = OutreachMessage::query()->latest('id')->first();
        $this->assertNotNull($message);
        $this->assertSame(OutreachMessage::STATUS_FAILED, $message->status);
        $this->assertNotNull($message->error);

        $enrollment->refresh();
        $this->assertSame(1, $enrollment->current_step, 'A failed send must not consume the step.');
        $this->assertSame(OutreachEnrollment::STATUS_ACTIVE, $enrollment->status);
        $this->assertTrue($enrollment->next_send_at->greaterThan(now()->addMinutes(30)));
    }

    public function test_a_sequence_gives_up_after_repeated_failures(): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => '127.0.0.1',
            'mail.mailers.smtp.port' => 1,
            'mail.mailers.smtp.username' => 'studio@example.com',
            'mail.mailers.smtp.timeout' => 1,
        ]);

        $campaign = $this->campaign();
        $prospect = $this->prospect();
        $enrollment = $this->enroll($campaign, $prospect);

        foreach (range(1, 3) as $attempt) {
            $this->travelTo(now()->addHours(2));
            app(OutreachDispatcher::class)->run();
            $this->assertSame($attempt, OutreachMessage::query()->where('status', OutreachMessage::STATUS_FAILED)->count());
        }

        $enrollment->refresh();
        $this->assertSame(OutreachEnrollment::STATUS_PAUSED, $enrollment->status);
        $this->assertStringContainsString('failed', (string) $enrollment->stop_reason);
    }

    public function test_a_log_transport_is_reported_as_not_delivered(): void
    {
        Mail::fake();
        config(['mail.default' => 'log']);

        $campaign = $this->campaign();
        $this->enroll($campaign, $this->prospect());

        $result = app(OutreachDispatcher::class)->run();
        $this->assertStringContainsString('Logged only', $result['message']);

        $message = OutreachMessage::query()->latest('id')->first();
        $this->assertNotNull($message);
        $this->assertSame(OutreachMessage::STATUS_SENT, $message->status);
        $this->assertFalse($message->wasDelivered());
        $this->assertSame('Logged only', $message->statusLabel());
    }

    public function test_the_sequence_finishes_after_the_last_step(): void
    {
        Mail::fake();

        $campaign = $this->campaign(steps: [
            ['delay_days' => 0, 'subject' => 'One', 'body' => 'One'],
        ]);

        $enrollment = $this->enroll($campaign, $this->prospect());

        app(OutreachDispatcher::class)->run();

        $enrollment->refresh();
        $this->assertSame(OutreachEnrollment::STATUS_FINISHED, $enrollment->status);
        $this->assertNull($enrollment->next_send_at);
    }

    public function test_the_lint_catches_the_copy_that_gets_cold_email_filtered(): void
    {
        $issues = OutreachLint::check('URGENT: ACT NOW', 'Click here to double your income! No obligation! Guaranteed income.');

        $this->assertTrue(OutreachLint::hasWarnings($issues));
        $this->assertStringContainsString('capitals', strtolower(implode(' ', array_column($issues, 'message'))));
        $this->assertStringContainsString('act now', strtolower(implode(' ', array_column($issues, 'message'))));
        $this->assertStringContainsString('personalised', strtolower(implode(' ', array_column($issues, 'message'))));

        // A first email claiming to be a reply is only caught when the position
        // is known.
        $re = OutreachLint::check('Re: your website', 'Hi {first_name}, a genuine question about {company}?', 1);
        $this->assertTrue(OutreachLint::hasWarnings($re));

        // The same subject on a follow-up is exactly right, so it must not be
        // reported — a check that cries wolf is one people learn to ignore.
        $this->assertFalse(OutreachLint::hasWarnings(
            OutreachLint::check('Re: your website', 'Hi {first_name}, following up on {company}?', 2),
        ));

        // A first email with nothing to answer is a wasted send; a follow-up
        // that closes the loop has nothing to ask, so it stays quiet.
        $statements = 'Hi {first_name}, I looked at {company} and made some notes about the site.';
        $this->assertContains('There is no question in the message, so there is nothing for the recipient to answer.', array_column(OutreachLint::check('A note about {company}', $statements, 1), 'message'));
        $this->assertNotContains('There is no question in the message, so there is nothing for the recipient to answer.', array_column(OutreachLint::check('Closing the loop', $statements.' That is all from me.', 2), 'message'));

        // And the demo copy is clean enough to be worth sending.
        $clean = OutreachLint::check(
            'Quick note about the {company} website',
            'Hi {first_name}, I had a look at {company} while researching {region}. Would a free ten-minute audit be worth ten minutes?',
        );
        $this->assertFalse(OutreachLint::hasWarnings($clean));
    }

    public function test_the_importer_reads_a_list_and_refuses_what_it_should(): void
    {
        $campaign = $this->campaign();

        EmailSuppression::add('opted-out@example.com', EmailSuppression::REASON_UNSUBSCRIBED);

        // A tab-separated export with a byte-order mark and mixed-case headers,
        // which is what Excel actually produces.
        $path = tempnam(sys_get_temp_dir(), 'outreach').'.csv';
        file_put_contents($path, implode("\n", [
            "\xEF\xBB\xBFCompany\tContact Name\tEmail Address\tSector\tCity",
            "Northgate Dental\tTom Brightwell\ttom@brightwell-dental.example.com\tDental care\tLeeds",
            "Copper Lane\t\tana@copper-lane.example.com\tInterior design\tBristol",
            "Bad Row\tNobody\tnot-an-address\t\t",
            "Opted Out Ltd\tSam\topted-out@example.com\t\t",
            "Northgate Dental\tTom Brightwell\ttom@brightwell-dental.example.com\tDental care\tLeeds",
        ]));

        $result = app(OutreachListImporter::class)->import($path, $campaign);

        unlink($path);

        $this->assertSame(2, $result['imported']);
        $this->assertSame(1, $result['duplicates']);
        $this->assertSame(1, $result['suppressed']);
        $this->assertSame(1, $result['invalid']);
        $this->assertSame(5, $result['total']);
        $this->assertFalse($result['missing_email_column']);
        $this->assertNotSame([], $result['problems']);

        $imported = OutreachProspect::query()->where('email', 'tom@brightwell-dental.example.com')->first();
        $this->assertNotNull($imported);
        $this->assertSame('Northgate Dental', $imported->company);
        $this->assertSame('Tom Brightwell', $imported->contact_name);
        $this->assertSame('Dental care', $imported->industry);
        $this->assertSame('Leeds', $imported->region);

        // The opted-out address must not exist as a prospect at all.
        $this->assertNull(OutreachProspect::query()->where('email', 'opted-out@example.com')->first());

        // Both new people were added to the campaign; the duplicate was not
        // added twice.
        $this->assertSame(2, $result['enrolled']);
        $this->assertSame(2, OutreachEnrollment::query()->where('outreach_campaign_id', $campaign->getKey())->count());
    }

    public function test_the_importer_reports_a_file_with_no_email_column(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'outreach').'.csv';
        file_put_contents($path, "name,city\nSomeone,Leeds\n");

        $result = app(OutreachListImporter::class)->import($path);

        unlink($path);

        $this->assertTrue($result['missing_email_column']);
        $this->assertSame(0, $result['imported']);
    }

    public function test_the_window_reports_its_next_opening(): void
    {
        config([
            'outreach.window.weekdays_only' => true,
            'outreach.window.start' => '09:00',
            'outreach.window.end' => '17:00',
        ]);

        $this->travelTo(Carbon::parse('next saturday 14:00', 'UTC'));

        $this->assertFalse(OutreachWindow::isOpen());
        $this->assertSame('the weekend', OutreachWindow::closure());

        $next = OutreachWindow::nextOpen();
        $this->assertTrue($next->isMonday());
        $this->assertSame('09:00', $next->format('H:i'));
    }

    public function test_the_enrollment_stops_rather_than_resets_when_somebody_is_added_twice(): void
    {
        $campaign = $this->campaign();
        $prospect = $this->prospect();

        $first = $this->enroll($campaign, $prospect);
        $this->assertNotNull($first);
        $first->update(['current_step' => 2]);

        $this->assertNull($this->enroll($campaign, $prospect));
        $this->assertSame('Already in this campaign.', app(OutreachDispatcher::class)->canEnroll($prospect, $campaign));
        $this->assertSame(2, $first->refresh()->current_step);
    }

    public function test_a_suppressed_prospect_cannot_be_added_to_a_campaign(): void
    {
        $campaign = $this->campaign();
        $prospect = $this->prospect();

        EmailSuppression::add($prospect->email, EmailSuppression::REASON_BOUNCED);

        $this->assertSame('On the opt-out list.', app(OutreachDispatcher::class)->canEnroll($prospect, $campaign));
        $this->assertSame(0, OutreachEnrollment::query()->where('outreach_campaign_id', $campaign->getKey())->count());
    }

    public function test_the_seeded_campaign_is_a_draft_so_the_demo_data_cannot_send(): void
    {
        $campaign = OutreachCampaign::query()->where('name', 'like', 'Website audit%')->first();

        $this->assertNotNull($campaign);
        $this->assertSame(OutreachCampaign::STATUS_DRAFT, $campaign->status);
        $this->assertSame(3, $campaign->steps()->count());
        $this->assertSame(0, app(OutreachDispatcher::class)->dueCount());

        foreach (OutreachProspect::query()->pluck('email') as $email) {
            $this->assertStringContainsString('example.com', $email);
        }
    }

    public function test_a_step_helper_reports_its_place_in_the_sequence(): void
    {
        $campaign = $this->campaign();
        $steps = $campaign->steps()->get();

        $this->assertSame('Step 1 · sent immediately', $steps[0]->label());
        $this->assertSame('Step 2 · sent 4 day(s) after the one before', $steps[1]->label());
        $this->assertInstanceOf(OutreachCampaignStep::class, $steps[2]);
    }
}
