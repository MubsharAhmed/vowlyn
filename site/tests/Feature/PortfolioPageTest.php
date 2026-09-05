<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PortfolioPageTest extends TestCase
{
    private const PROJECTS = [
        'Moventra Distribution' => 'https://www.moventradistribution.com/',
        'Meljori Jewellery' => 'https://www.meljorijewellery.ca/',
        'IT Bridges' => 'https://itbridges.ca/',
        'Persian Designer Rugs' => 'https://persiandesignerrugs.ca/',
        'Arian Rugs' => 'https://arianrugs.com/',
        'Burloak Painting' => 'https://burlingtonspainters.com/',
    ];

    public function test_home_and_portfolio_show_the_selected_client_projects(): void
    {
        foreach (['/', '/portfolio'] as $uri) {
            $response = $this->get($uri);

            $response->assertOk();

            foreach (self::PROJECTS as $name => $url) {
                $response->assertSee($name);
                $response->assertSee('href="'.$url.'"', false);
            }
        }
    }

    public function test_removed_placeholder_projects_are_not_rendered(): void
    {
        $response = $this->get('/portfolio');

        $response->assertOk();
        $response->assertDontSee('Helio Banking');
        $response->assertDontSee('Atlas Studio');
        $response->assertDontSee('Lumen AI');
        $response->assertDontSee('United Buying Group');
    }

    public function test_portfolio_grid_renders_an_image_for_every_project(): void
    {
        $response = $this->get('/portfolio');

        $response->assertOk();
        $response->assertSee('alt="Electronics and accessories representing the Moventra Distribution project"', false);
        $response->assertSee('alt="Fine jewellery representing the Meljori Jewellery project"', false);
        $response->assertSee('alt="Software development workspace representing the IT Bridges project"', false);
        $response->assertSee('alt="Patterned interior rug representing the Persian Designer Rugs project"', false);
        $response->assertSee('alt="Refined home interior representing the Arian Rugs project"', false);
        $response->assertSee('alt="Professional painter representing the Burloak Painting project"', false);
    }

    public function test_booking_calls_link_to_calendly(): void
    {
        foreach (['/about', '/services', '/why-us', '/services/web-app-development'] as $uri) {
            $response = $this->get($uri);

            $response->assertOk();
            $response->assertSee('href="https://calendly.com/junaidswati/new-meeting"', false);
        }
    }
}
