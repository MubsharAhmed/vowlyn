<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PerformanceMarketingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_performance_marketing_page_replaces_marketplace_and_has_honest_service_content(): void
    {
        $this->get('/performance-marketing')
            ->assertOk()
            ->assertSee('Turn attention into', false)
            ->assertSee('Editorial illustration showing attention becoming intent and action', false)
            ->assertSee('Attention', false)
            ->assertSee('Intent', false)
            ->assertSee('Paid search')
            ->assertSee('Conversion tracking')
            ->assertSee('without promising results before seeing the evidence')
            ->assertDontSee('Growth operating system')
            ->assertDontSee('Verified events')
            ->assertDontSee('Active Listings')
            ->assertDontSee('priced from $79');

        $this->get('/marketplace')->assertRedirect('/performance-marketing')->assertStatus(301);
        $this->get('/')->assertSee('Marketing')->assertDontSee('>Marketplace<', false);
        $this->get('/sitemap.xml')->assertOk()->assertSee('/performance-marketing')->assertDontSee('<loc>http://localhost/marketplace</loc>', false);
    }
}
