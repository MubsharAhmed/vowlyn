<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

final class GoogleAnalyticsTest extends TestCase
{
    public function test_google_analytics_is_not_rendered_without_a_measurement_id(): void
    {
        Config::set('services.google_analytics.measurement_id');

        $this->get('/')
            ->assertOk()
            ->assertDontSee('googletagmanager.com/gtag/js', false)
            ->assertDontSee("gtag('config'", false);
    }

    public function test_direct_google_tag_is_rendered_for_the_vowlyn_data_stream(): void
    {
        Config::set('services.google_analytics.measurement_id', 'G-XBKNTW9W3M');

        $this->get('/')
            ->assertOk()
            ->assertSee('googletagmanager.com/gtag/js?id=G-XBKNTW9W3M', false)
            ->assertSee("gtag('config', \"G-XBKNTW9W3M\")", false)
            ->assertSee('window.dataLayer = window.dataLayer || []', false);
    }

    public function test_invalid_measurement_ids_are_never_rendered(): void
    {
        Config::set('services.google_analytics.measurement_id', '"><script>alert(1)</script>');

        $this->get('/')
            ->assertOk()
            ->assertDontSee('googletagmanager.com/gtag/js', false)
            ->assertDontSee('alert(1)', false);
    }

    public function test_successful_lead_event_is_sent_to_ga4(): void
    {
        Config::set('services.google_analytics.measurement_id', 'G-XBKNTW9W3M');

        $this->withSession([
            'analytics.event' => [
                'event' => 'generate_lead',
                'form_source' => 'hero',
                'service' => 'web-app-development',
            ],
        ])->get('/')
            ->assertOk()
            ->assertSee("gtag('event', \"generate_lead\"", false)
            ->assertSee('web-app-development', false);
    }
}
