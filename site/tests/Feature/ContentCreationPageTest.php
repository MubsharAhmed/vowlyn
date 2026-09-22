<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ContentCreationPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_creation_page_renders_the_video_collection(): void
    {
        $response = $this->get('/content-creation');

        $response->assertOk();
        $response->assertSee('Make them', false);
        $response->assertSee('Caledon Golf Club');
        $response->assertSee('media/content-creation/caledon-brand-film.mp4', false);
        $response->assertSee('id="photography"', false);
        $response->assertSee('id="design"', false);
        $response->assertSee('Moventra Distribution');
        $response->assertSee('loading="lazy"', false);
    }
}
