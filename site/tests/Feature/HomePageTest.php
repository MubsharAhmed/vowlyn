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
        $response->assertSee('id="about"', false);
        $response->assertSee('id="process"', false);
        $response->assertSee('id="projects"', false);
        $response->assertSee('id="contact"', false);
    }
}
