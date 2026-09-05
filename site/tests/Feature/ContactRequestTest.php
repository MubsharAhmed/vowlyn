<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ContactRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ContactRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_submission_creates_record_and_redirects(): void
    {
        $payload = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'service' => 'saas-development',
            'brief' => 'We need a scalable SaaS platform with admin tooling and modern UI. Help!',
            'website' => '', // honeypot left empty
        ];

        $response = $this->post('/contact', $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('contact_requests', [
            'email' => 'jane@example.com',
            'name' => 'Jane Doe',
            'service' => 'saas-development',
            'status' => ContactRequest::STATUS_NEW,
        ]);

        $record = ContactRequest::query()->latest('id')->first();
        $this->assertNotNull($record);
        $this->assertNotNull($record->ip_address);
        $this->assertNotNull($record->user_agent);
    }

    public function test_invalid_submission_fails_validation(): void
    {
        $response = $this->from('/#contact')->post('/contact', [
            'name' => 'A', // too short
            'email' => 'not-an-email',
            'brief' => 'too short',
        ]);

        $response->assertInvalid(['name', 'email', 'brief']);
        $this->assertDatabaseCount('contact_requests', 0);
    }

    public function test_honeypot_field_blocks_bots(): void
    {
        $response = $this->from('/#contact')->post('/contact', [
            'name' => 'Spam Bot',
            'email' => 'bot@example.com',
            'brief' => 'Buy cheap pills now visit our amazing store right away!!!',
            'website' => 'http://spam.example.com', // honeypot filled = bot
        ]);

        $response->assertInvalid(['website']);
        $this->assertDatabaseCount('contact_requests', 0);
    }

    public function test_service_must_be_one_of_the_published_services(): void
    {
        $response = $this->from('/#home')->post('/contact', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'service' => 'not-a-real-service',
            'brief' => 'We need help building and launching a new digital product.',
            'website' => '',
        ]);

        $response->assertInvalid(['service']);
        $this->assertDatabaseCount('contact_requests', 0);
    }
}
