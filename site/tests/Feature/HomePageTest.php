<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class HomePageTest extends TestCase
{
    public function test_home_page_returns_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Vowlyn', false);
    }

    public function test_home_page_renders_core_sections(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        // Section anchors used by the navigation
        $response->assertSee('id="services"', false);
        $response->assertSee('id="process"', false);
        $response->assertSee('id="projects"', false);
        $response->assertSee('id="contact"', false);
        $response->assertDontSee('id="about"', false);
        $response->assertDontSee('id="reel"', false);
    }

    public function test_hero_contains_the_project_request_form_and_service_options(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('id="hero-contact-form"', false);
        $response->assertSee('name="service"', false);
        $response->assertSee('Web App Development');
        $response->assertSee('AI Engineering &amp; Integration', false);
        $response->assertDontSee('data-hero-scene', false);
    }

    public function test_mobile_navigation_uses_an_immediately_interactive_native_disclosure(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('data-mobile-menu', false);
        $response->assertSee('<summary', false);
        $response->assertDontSee('x-data="{ open: false }"', false);
    }

    public function test_about_specialist_layout_contains_mobile_width_constraints(): void
    {
        $response = $this->get('/about');

        $response->assertOk();
        $response->assertSee('lg:grid-cols-[280px_minmax(0,1fr)]', false);
        $response->assertSee('relative min-w-0 min-h-[36rem]', false);
    }
}
