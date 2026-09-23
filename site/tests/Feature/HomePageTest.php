<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Support\ServiceCatalog;
use Tests\TestCase;

final class HomePageTest extends TestCase
{
    public function test_home_page_returns_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Vowlyn', false);
    }

    public function test_footer_and_organization_schema_list_only_the_profiles_we_maintain(): void
    {
        $response = $this->get('/');
        $linkedin = (string) config('services.social.linkedin');

        $response->assertOk()
            ->assertSee('"sameAs"', false)
            ->assertSee($linkedin, false)
            ->assertDontSee('Dribbble')
            ->assertDontSee('X / Twitter')
            ->assertDontSee('x.com/vowlyn', false)
            ->assertDontSee('dribbble.com/vowlyn', false);

        // Linked from the footer and declared as the organization profile.
        $this->assertSame(2, substr_count($response->getContent(), $linkedin));
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
        $response->assertSee('Content Creation, Photography &amp; Design', false);
        $response->assertSee('Marketing');
        $response->assertDontSee('data-hero-scene', false);
    }

    public function test_mobile_navigation_uses_an_immediately_interactive_native_disclosure(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('data-mobile-menu', false);
        $response->assertSee('data-mobile-panel', false);
        $response->assertSee('<summary', false);
        $response->assertDontSee('x-data="{ open: false }"', false);
        $response->assertSee('Marketing');
    }

    public function test_services_navigation_lists_every_discipline_and_a_call_to_action(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('data-services-trigger', false);
        $response->assertSee('data-services-panel', false);
        $response->assertSee('aria-haspopup="true"', false);

        foreach (ServiceCatalog::list() as $service) {
            $response->assertSee(route('services.show', $service['slug']), false);
            $response->assertSee($service['name']);
        }

        $response->assertSee(route('services'), false);
        $response->assertSee('All services');
        $response->assertSee('Start a project');
    }

    public function test_about_specialist_layout_contains_mobile_width_constraints(): void
    {
        $response = $this->get('/about');

        $response->assertOk();
        $response->assertSee('lg:grid-cols-[280px_minmax(0,1fr)]', false);
        $response->assertSee('relative min-w-0 min-h-[36rem]', false);
    }
}
