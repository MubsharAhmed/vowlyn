<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\EmailSuppressions\Pages\ListEmailSuppressions;
use App\Filament\Resources\OutreachCampaigns\Pages\CreateOutreachCampaign;
use App\Filament\Resources\OutreachCampaigns\Pages\EditOutreachCampaign;
use App\Filament\Resources\OutreachCampaigns\Pages\ListOutreachCampaigns;
use App\Filament\Resources\OutreachCampaigns\RelationManagers\EnrollmentsRelationManager;
use App\Filament\Resources\OutreachProspects\Pages\EditOutreachProspect;
use App\Filament\Resources\OutreachProspects\Pages\ListOutreachProspects;
use App\Filament\Resources\OutreachProspects\RelationManagers\MessagesRelationManager;
use App\Filament\Widgets\OutreachOverview;
use App\Models\EmailSuppression;
use App\Models\OutreachCampaign;
use App\Models\OutreachEnrollment;
use App\Models\OutreachMessage;
use App\Models\OutreachProspect;
use App\Models\User;
use App\Policies\EmailSuppressionPolicy;
use App\Policies\OutreachCampaignPolicy;
use App\Policies\OutreachEnrollmentPolicy;
use App\Policies\OutreachMessagePolicy;
use App\Policies\OutreachProspectPolicy;
use App\Services\OutreachDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

final class OutreachPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'outreach.window.start' => '00:00',
            'outreach.window.end' => '23:59',
            'outreach.window.weekdays_only' => false,
            'outreach.interval_seconds' => 0,
            'outreach.timezone' => 'UTC',
            'mail.default' => 'log',
            'mail.reply_to.address' => 'hello@example.com',
            'mail.reply_to.name' => 'Vowlyn',
            'outreach.footer.postal_address' => '1 Test Street, London',
        ]);
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['email_verified_at' => now()])->save();

        return $user;
    }

    private function campaign(): OutreachCampaign
    {
        $campaign = OutreachCampaign::create([
            'name' => 'Audit sequence',
            'goal' => 'a free website audit',
            'status' => OutreachCampaign::STATUS_ACTIVE,
        ]);

        $campaign->steps()->create([
            'position' => 1,
            'delay_days' => 0,
            'subject' => 'A note about {company}',
            'body' => 'Hi {first_name}, would {company} like a free audit?',
        ]);

        return $campaign->refresh();
    }

    private function prospect(array $attributes = []): OutreachProspect
    {
        return OutreachProspect::create(array_replace([
            'company' => 'Halstead & Rowe',
            'contact_name' => 'Priya Halstead',
            'email' => 'priya@halstead.example.com',
            'status' => OutreachProspect::STATUS_NEW,
        ], $attributes));
    }

    public function test_the_outreach_screens_open_for_a_verified_admin(): void
    {
        $this->actingAs($this->admin());

        $campaign = $this->campaign();
        $prospect = $this->prospect();

        $this->get(ListOutreachCampaigns::getUrl())->assertOk();
        $this->get(CreateOutreachCampaign::getUrl())->assertOk();
        $this->get(EditOutreachCampaign::getUrl(['record' => $campaign]))->assertOk();
        $this->get(ListOutreachProspects::getUrl())->assertOk();
        $this->get(ListEmailSuppressions::getUrl())->assertOk();
        $this->get(EditOutreachProspect::getUrl(['record' => $prospect]))->assertOk();
    }

    public function test_a_new_campaign_is_always_saved_as_a_draft(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateOutreachCampaign::class)
            ->fillForm([
                'name' => 'Quick win sequence',
                'goal' => 'a free audit',
                // Deliberately trying to start sending in the same step that
                // creates the campaign.
                'status' => OutreachCampaign::STATUS_ACTIVE,
                'steps' => [
                    [
                        'delay_days' => 0,
                        'subject' => 'A note about {company}',
                        'body' => 'Hi {first_name}, would {company} like a free audit?',
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $campaign = OutreachCampaign::query()->where('name', 'Quick win sequence')->first();

        $this->assertNotNull($campaign);
        $this->assertSame(OutreachCampaign::STATUS_DRAFT, $campaign->status);
    }

    public function test_the_send_now_action_reports_what_it_did(): void
    {
        Mail::fake();
        $this->actingAs($this->admin());

        $campaign = $this->campaign();
        app(OutreachDispatcher::class)->enroll($this->prospect(), $campaign);

        Livewire::test(ListOutreachCampaigns::class)
            ->callAction('sendDueNow')
            ->assertNotified();

        Mail::assertSentCount(1);
        $this->assertSame(1, OutreachMessage::query()->where('status', OutreachMessage::STATUS_SENT)->count());
    }

    public function test_the_campaign_people_tab_renders_and_can_send_one_person(): void
    {
        Mail::fake();
        $this->actingAs($this->admin());

        $campaign = $this->campaign();
        $enrollment = app(OutreachDispatcher::class)->enroll($this->prospect(), $campaign);

        // This catches the class of bug where a relation manager silently
        // disappears because its related model has no policy.
        Livewire::test(EnrollmentsRelationManager::class, [
            'ownerRecord' => $campaign,
            'pageClass' => EditOutreachCampaign::class,
        ])
            ->assertOk()
            ->assertCanSeeTableRecords([$enrollment])
            ->callTableAction('sendNow', $enrollment)
            ->assertNotified();

        Mail::assertSentCount(1);
    }

    public function test_the_prospect_send_log_is_reachable_from_the_person(): void
    {
        $this->actingAs($this->admin());

        $campaign = $this->campaign();
        $prospect = $this->prospect();
        app(OutreachDispatcher::class)->enroll($prospect, $campaign);
        app(OutreachDispatcher::class)->run();

        $message = OutreachMessage::query()->firstOrFail();

        Livewire::test(MessagesRelationManager::class, [
            'ownerRecord' => $prospect,
            'pageClass' => EditOutreachProspect::class,
        ])
            ->assertOk()
            ->assertCanSeeTableRecords([$message]);
    }

    public function test_marking_a_prospect_as_replied_in_the_panel_stops_their_sequences(): void
    {
        Mail::fake();
        $this->actingAs($this->admin());

        $campaign = $this->campaign();
        $prospect = $this->prospect();
        $enrollment = app(OutreachDispatcher::class)->enroll($prospect, $campaign);

        Livewire::test(ListOutreachProspects::class)
            ->callTableAction('markReplied', $prospect)
            ->assertNotified();

        $this->assertSame(OutreachProspect::STATUS_REPLIED, $prospect->refresh()->status);
        $this->assertSame(OutreachEnrollment::STATUS_REPLIED, $enrollment->refresh()->status);

        // And the scheduled follow-up will not go out.
        app(OutreachDispatcher::class)->run();
        Mail::assertNothingSent();
    }

    public function test_adding_a_prospect_to_the_opt_out_list_from_the_panel_stops_everything(): void
    {
        Mail::fake();
        $this->actingAs($this->admin());

        $campaign = $this->campaign();
        $prospect = $this->prospect();
        $enrollment = app(OutreachDispatcher::class)->enroll($prospect, $campaign);

        Livewire::test(ListOutreachProspects::class)
            ->callTableBulkAction('suppress', [$prospect], [
                'reason' => EmailSuppression::REASON_ASKED,
                'note' => 'Asked on the phone',
            ])
            ->assertNotified();

        $this->assertTrue(EmailSuppression::covers($prospect->email));
        $this->assertSame(OutreachEnrollment::STATUS_UNSUBSCRIBED, $enrollment->refresh()->status);

        app(OutreachDispatcher::class)->run();
        Mail::assertNothingSent();
    }

    public function test_the_campaign_people_screen_offers_a_way_to_add_people(): void
    {
        $this->actingAs($this->admin());

        $campaign = $this->campaign();

        // A button nobody can find is the same as no button, so this asserts the
        // action is really registered in the table header.
        Livewire::test(EnrollmentsRelationManager::class, [
            'ownerRecord' => $campaign,
            'pageClass' => EditOutreachCampaign::class,
        ])
            ->assertOk()
            ->assertTableHeaderActionsExistInOrder(['enrollPeople']);
    }

    public function test_adding_people_to_a_campaign_from_the_prospects_list(): void
    {
        $this->actingAs($this->admin());

        $campaign = $this->campaign();
        $prospect = $this->prospect();
        $suppressed = $this->prospect(['email' => 'stopped@example.com']);
        EmailSuppression::add($suppressed->email, EmailSuppression::REASON_ASKED);

        Livewire::test(ListOutreachProspects::class)
            ->callTableBulkAction('addToCampaign', [$prospect, $suppressed], [
                'campaign_id' => $campaign->getKey(),
            ])
            ->assertNotified();

        $this->assertSame(1, OutreachEnrollment::query()->where('outreach_campaign_id', $campaign->getKey())->count());
        $this->assertSame(1, OutreachEnrollment::query()->where('outreach_prospect_id', $prospect->getKey())->count());
        $this->assertSame(0, OutreachEnrollment::query()->where('outreach_prospect_id', $suppressed->getKey())->count());
    }

    public function test_the_outreach_widget_summarises_the_state(): void
    {
        $this->actingAs($this->admin());

        $campaign = $this->campaign();
        app(OutreachDispatcher::class)->enroll($this->prospect(), $campaign);

        Livewire::test(OutreachOverview::class)
            ->assertOk()
            ->assertSee('Ready to send');

        // With mail on the log transport the widget says so, because a campaign
        // that appears to run while nothing is delivered is the failure worth
        // shouting about.
        Livewire::test(OutreachOverview::class)->assertSee('Email is not configured');
    }

    public function test_the_opt_out_page_asks_before_it_acts(): void
    {
        // Mail scanners follow links in incoming mail before the recipient sees
        // them, so a GET must never unsubscribe anybody.
        $prospect = $this->prospect();

        $this->get(route('outreach.unsubscribe', ['token' => $prospect->unsubscribe_token]))
            ->assertOk()
            ->assertSee('Confirm you want to opt out');

        $this->assertFalse(EmailSuppression::covers($prospect->email));
        $this->assertSame(OutreachProspect::STATUS_NEW, $prospect->refresh()->status);
    }

    public function test_confirming_the_opt_out_stops_everything_and_can_be_undone(): void
    {
        $campaign = $this->campaign();
        $prospect = $this->prospect();
        $enrollment = app(OutreachDispatcher::class)->enroll($prospect, $campaign);

        $this->post(route('outreach.unsubscribe.confirm', ['token' => $prospect->unsubscribe_token]))
            ->assertRedirect(route('outreach.unsubscribe', ['token' => $prospect->unsubscribe_token]));

        $this->assertTrue(EmailSuppression::covers($prospect->email));
        $this->assertSame(OutreachProspect::STATUS_UNSUBSCRIBED, $prospect->refresh()->status);
        $this->assertSame(OutreachEnrollment::STATUS_UNSUBSCRIBED, $enrollment->refresh()->status);

        // Somebody who clicked by mistake is not trapped.
        $this->post(route('outreach.unsubscribe.undo', ['token' => $prospect->unsubscribe_token]))
            ->assertRedirect();

        $this->assertFalse(EmailSuppression::covers($prospect->email));

        // But the stopped sequence stays stopped — undoing an opt-out is not the
        // same as asking to be written to again.
        $this->assertSame(OutreachEnrollment::STATUS_UNSUBSCRIBED, $enrollment->refresh()->status);
    }

    public function test_a_one_click_unsubscribe_from_a_mail_client_needs_no_token_or_session(): void
    {
        $prospect = $this->prospect();

        $response = $this->withHeaders(['Accept' => '*/*'])
            ->post(route('outreach.unsubscribe.confirm', ['token' => $prospect->unsubscribe_token]));

        $response->assertOk();
        $this->assertStringContainsString('unsubscribed', $response->getContent());
        $this->assertTrue(EmailSuppression::covers($prospect->email));
    }

    public function test_an_unknown_opt_out_token_is_a_404(): void
    {
        $this->get(route('outreach.unsubscribe', ['token' => str_repeat('a', 40)]))->assertNotFound();
        $this->get('/unsubscribe/short')->assertNotFound();
    }

    public function test_the_opt_out_page_is_not_indexed(): void
    {
        $prospect = $this->prospect();

        $this->get(route('outreach.unsubscribe', ['token' => $prospect->unsubscribe_token]))
            ->assertSee('noindex,nofollow', false);
    }

    public function test_every_outreach_model_has_a_policy_so_nothing_is_silently_hidden(): void
    {
        $this->assertInstanceOf(OutreachCampaignPolicy::class, Gate::getPolicyFor(OutreachCampaign::class));
        $this->assertInstanceOf(OutreachProspectPolicy::class, Gate::getPolicyFor(OutreachProspect::class));
        $this->assertInstanceOf(OutreachEnrollmentPolicy::class, Gate::getPolicyFor(OutreachEnrollment::class));
        $this->assertInstanceOf(OutreachMessagePolicy::class, Gate::getPolicyFor(OutreachMessage::class));
        $this->assertInstanceOf(EmailSuppressionPolicy::class, Gate::getPolicyFor(EmailSuppression::class));

        // The send log is evidence, not a workspace: it cannot be edited or
        // deleted from the panel.
        $user = $this->admin();
        $message = OutreachMessage::query()->first();

        $this->assertTrue(Gate::forUser($user)->allows('viewAny', OutreachMessage::class));
        $this->assertFalse(Gate::forUser($user)->allows('create', OutreachMessage::class));
    }

    public function test_an_unverified_user_cannot_reach_the_outreach_screens(): void
    {
        $this->actingAs(User::factory()->unverified()->create());

        $this->get(ListOutreachProspects::getUrl())->assertForbidden();
        $this->get(ListOutreachCampaigns::getUrl())->assertForbidden();
        $this->get(ListEmailSuppressions::getUrl())->assertForbidden();
    }
}
