<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\ContactRequests\Pages\EditContactRequest;
use App\Filament\Resources\ContactRequests\RelationManagers\MessagesRelationManager;
use App\Filament\Resources\MailTemplates\Pages\CreateMailTemplate;
use App\Filament\Resources\MailTemplates\Pages\ListMailTemplates;
use App\Mail\LeadReply;
use App\Models\ContactRequest;
use App\Models\ContactRequestMessage;
use App\Models\MailTemplate;
use App\Models\User;
use App\Support\ServiceCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;

final class LeadReplyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A mailer that really delivers, so "sent" in these tests means delivered.
     */
    private function configureDeliverableMail(): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.example.com',
            'mail.mailers.smtp.username' => 'studio@example.com',
        ]);
    }

    private function lead(array $attributes = []): ContactRequest
    {
        return ContactRequest::create(array_replace([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'company' => 'Northwind Ltd',
            'service' => 'ai-development',
            'brief' => 'We want an assistant grounded in our own documentation.',
            'status' => ContactRequest::STATUS_NEW,
        ], $attributes));
    }

    public function test_every_service_has_a_professional_reply_ready_to_send(): void
    {
        $this->assertSame(count(ServiceCatalog::options()) + 1, MailTemplate::count());

        foreach (ServiceCatalog::options() as $service => $label) {
            $this->assertNotNull(
                MailTemplate::query()->where('service', $service)->first(),
                "No reply template exists for {$service} ({$label}).",
            );
        }

        $this->assertNotNull(MailTemplate::query()->whereNull('service')->first(), 'There is no general fallback reply.');
        $this->assertSame('Any service', MailTemplate::query()->whereNull('service')->firstOrFail()->serviceLabel());
    }

    public function test_templates_only_use_placeholders_that_are_filled_from_the_enquiry(): void
    {
        $tokens = $this->lead()->replyTokens();

        foreach (MailTemplate::all() as $template) {
            $this->assertStringNotContainsString(
                '{',
                $template->renderSubject($tokens).$template->renderBody($tokens),
                "Template “{$template->name}” uses a placeholder nothing replaces.",
            );
        }

        $rendered = MailTemplate::query()->where('service', 'ai-development')->firstOrFail()->renderBody($tokens);

        $this->assertStringContainsString('Jane Doe', $rendered);
        $this->assertStringContainsString('Northwind Ltd', $rendered);
        $this->assertStringNotContainsString('{', $rendered);

        // The general reply names the service, which is what makes it readable
        // when the lead themselves were not sure which service they needed.
        $general = MailTemplate::query()->whereNull('service')->firstOrFail()->renderBody($tokens);

        $this->assertStringContainsString('AI Engineering & Integration', $general);
    }

    public function test_verified_admin_can_send_a_reply_that_is_logged_and_closes_the_lead(): void
    {
        $this->configureDeliverableMail();
        Mail::fake();
        $this->actingAs(User::factory()->create());

        $lead = $this->lead();
        $template = MailTemplate::query()->where('service', 'ai-development')->firstOrFail();

        Livewire::test(EditContactRequest::class, ['record' => $lead->id])
            ->callAction('sendReply', data: [
                'mail_template_id' => $template->id,
                'subject' => 'Bringing AI into Northwind',
                'body' => "Hi Jane,\n\nThanks for the detail about your documentation.",
            ])
            ->assertHasNoActionErrors()
            ->assertNotified();

        Mail::assertSent(LeadReply::class, fn (LeadReply $mail): bool => $mail->hasTo('jane@example.com'));

        $lead->refresh();
        $this->assertSame(ContactRequest::STATUS_REPLIED, $lead->status);
        $this->assertNotNull($lead->replied_at);

        $message = $lead->messages()->firstOrFail();
        $this->assertSame(ContactRequestMessage::STATUS_SENT, $message->status);
        $this->assertSame('smtp', $message->transport);
        $this->assertTrue($message->wasDelivered());
        $this->assertSame('Sent', $message->statusLabel());
        $this->assertSame($template->id, $message->mail_template_id);
        $this->assertNotNull($message->sent_at);
    }

    public function test_a_rejected_send_is_recorded_and_the_lead_is_not_marked_replied(): void
    {
        $this->configureDeliverableMail();
        Mail::shouldReceive('to')->andReturnSelf();
        Mail::shouldReceive('send')->andThrow(new RuntimeException('SMTP authentication failed'));
        $this->actingAs(User::factory()->create());

        $lead = $this->lead();

        Livewire::test(EditContactRequest::class, ['record' => $lead->id])
            ->callAction('sendReply', data: [
                'mail_template_id' => null,
                'subject' => 'A reply that will fail',
                'body' => 'This message cannot be delivered because the mail server refused it.',
            ])
            ->assertNotified();

        $lead->refresh();
        $this->assertSame(ContactRequest::STATUS_NEW, $lead->status);
        $this->assertNull($lead->replied_at);

        $message = $lead->messages()->firstOrFail();
        $this->assertTrue($message->failed());
        $this->assertFalse($message->wasDelivered());
        $this->assertSame('Failed', $message->statusLabel());
        $this->assertStringContainsString('SMTP authentication failed', (string) $message->error);
    }

    public function test_replies_sent_before_mail_is_configured_are_recorded_but_never_called_delivered(): void
    {
        config(['mail.default' => 'log']);
        Mail::fake();
        $this->actingAs(User::factory()->create());

        $lead = $this->lead();

        Livewire::test(EditContactRequest::class, ['record' => $lead->id])
            ->assertSee('Replies cannot be delivered yet')
            ->callAction('sendReply', data: [
                'mail_template_id' => null,
                'subject' => 'Written before mail was configured',
                'body' => 'The lead would not receive this yet, and the panel says so.',
            ])
            ->assertNotified();

        $message = $lead->messages()->firstOrFail();
        $this->assertSame('log', $message->transport);
        $this->assertFalse($message->wasDelivered());
        $this->assertSame('Logged only', $message->statusLabel());

        // Nobody received anything, so the lead has not been answered.
        $lead->refresh();
        $this->assertSame(ContactRequest::STATUS_NEW, $lead->status);
        $this->assertNull($lead->replied_at);
    }

    public function test_the_reply_history_lists_every_message_and_what_happened_to_it(): void
    {
        $this->configureDeliverableMail();
        Mail::fake();
        $this->actingAs(User::factory()->create());

        $lead = $this->lead();

        Livewire::test(EditContactRequest::class, ['record' => $lead->id])
            ->callAction('sendReply', data: [
                'mail_template_id' => null,
                'subject' => 'Read back in the history',
                'body' => 'The body of the reply should be readable afterwards.',
            ])
            ->assertNotified();

        // The history has to be reachable from the request page, not merely
        // renderable on its own — Filament drops a relation manager whose
        // related model has no policy, which is easy to miss.
        $page = Livewire::test(EditContactRequest::class, ['record' => $lead->id]);
        $this->assertContains(MessagesRelationManager::class, $page->instance()->getRelationManagers());

        Livewire::test(MessagesRelationManager::class, [
            'ownerRecord' => $lead,
            'pageClass' => EditContactRequest::class,
        ])
            ->assertOk()
            ->assertCanSeeTableRecords($lead->messages()->get())
            ->assertSee('Read back in the history')
            ->assertSee('Sent');
    }

    public function test_the_reply_dialog_prefills_the_template_for_the_leads_service(): void
    {
        $this->actingAs(User::factory()->create());

        $lead = $this->lead(['service' => 'ai-development']);
        $template = MailTemplate::query()->where('service', 'ai-development')->firstOrFail();
        $tokens = $lead->replyTokens();

        Livewire::test(EditContactRequest::class, ['record' => $lead->id])
            ->mountAction('sendReply')
            ->assertActionMounted('sendReply')
            ->assertActionDataSet([
                'subject' => $template->renderSubject($tokens),
                'body' => $template->renderBody($tokens),
            ]);
    }

    public function test_replies_are_offered_for_the_leads_service_first_then_general_ones(): void
    {
        $choices = MailTemplate::choicesFor('enterprise-security');

        $this->assertSame('enterprise-security', $choices->first()->service);
        $this->assertNull($choices->get(1)->service);
        $this->assertCount(count(ServiceCatalog::options()) + 1, $choices);

        $inactive = MailTemplate::query()->where('service', 'cloud-devops')->firstOrFail();
        $inactive->update(['is_active' => false]);
        $this->assertNotContains($inactive->id, MailTemplate::choicesFor('cloud-devops')->pluck('id')->all());
    }

    public function test_everything_typed_into_a_reply_is_escaped_in_the_email(): void
    {
        $lead = $this->lead(['brief' => '<script>alert("brief")</script>']);

        $html = (new LeadReply($lead, 'Subject <b>bold</b>', "Hi <script>alert('body')</script>"))->render();

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('alert("brief")</script>', $html);
    }

    public function test_mail_templates_are_managed_by_verified_admins_only(): void
    {
        $this->get('/admin/mail-templates')->assertRedirect();

        $unverified = User::factory()->unverified()->create();
        $this->assertFalse(Gate::forUser($unverified)->allows('create', MailTemplate::class));
        $this->actingAs($unverified)->get('/admin/mail-templates')->assertForbidden();

        $this->actingAs(User::factory()->create());
        Livewire::test(ListMailTemplates::class)->assertCanSeeTableRecords(MailTemplate::all());

        Livewire::test(CreateMailTemplate::class)
            ->fillForm([
                'service' => 'mobile-app-development',
                'name' => 'Mobile — friendly follow-up',
                'subject' => 'Following up on your app idea',
                'body' => 'Hi {name}, a short follow-up about the mobile project at {company}.',
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertDatabaseHas('mail_templates', [
            'service' => 'mobile-app-development',
            'name' => 'Mobile — friendly follow-up',
        ]);
    }
}
