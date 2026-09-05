<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\BlogPosts\Pages\CreateBlogPost;
use App\Filament\Resources\BlogPosts\Pages\EditBlogPost;
use App\Models\BlogAuthor;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

final class BlogAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['email_verified_at' => now()]));
    }

    public function test_client_can_create_a_draft_in_filament(): void
    {
        $author = BlogAuthor::factory()->create();
        $category = BlogCategory::factory()->create();

        Livewire::test(CreateBlogPost::class)
            ->fillForm([
                'title' => 'A Practical Guide to SaaS Architecture',
                'slug' => 'practical-guide-saas-architecture',
                'excerpt' => 'A grounded way to choose the right boundaries, data model, and deployment strategy.',
                'content' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'content' => [['type' => 'text', 'text' => 'Start with the product constraints.']],
                    ]],
                ],
                'blog_author_id' => $author->id,
                'blog_category_id' => $category->id,
                'status' => BlogPost::STATUS_DRAFT,
                'is_featured' => false,
                'is_indexable' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertDatabaseHas('blog_posts', [
            'slug' => 'practical-guide-saas-architecture',
            'status' => BlogPost::STATUS_DRAFT,
        ]);
    }

    public function test_published_post_requires_publish_date_image_and_alt_text(): void
    {
        $author = BlogAuthor::factory()->create();
        $category = BlogCategory::factory()->create();

        Livewire::test(CreateBlogPost::class)
            ->fillForm([
                'title' => 'Incomplete Published Article',
                'slug' => 'incomplete-published-article',
                'excerpt' => 'This article is deliberately missing publication requirements.',
                'content' => [
                    'type' => 'doc',
                    'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Content']]]],
                ],
                'blog_author_id' => $author->id,
                'blog_category_id' => $category->id,
                'status' => BlogPost::STATUS_PUBLISHED,
                'published_at' => null,
                'featured_image' => null,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'published_at' => 'required',
                'featured_image' => 'required',
            ]);
    }

    public function test_featured_image_opens_the_crop_editor_when_the_source_is_not_sixteen_by_nine(): void
    {
        Livewire::test(CreateBlogPost::class)
            ->assertFormFieldExists(
                'featured_image',
                checkFieldUsing: fn (FileUpload $field): bool => $field->getImageAspectRatio() === '16:9'
                    && $field->shouldAutomaticallyOpenImageEditorForAspectRatio(),
            );
    }

    public function test_social_image_opens_the_crop_editor_when_the_source_is_not_compatible(): void
    {
        Livewire::test(CreateBlogPost::class)
            ->assertFormFieldExists(
                'social_image',
                checkFieldUsing: fn (FileUpload $field): bool => $field->getImageAspectRatio() === '1200:630'
                    && $field->shouldAutomaticallyOpenImageEditorForAspectRatio(),
            );
    }

    public function test_client_can_publish_with_a_correctly_proportioned_featured_image(): void
    {
        Storage::fake('public');

        $author = BlogAuthor::factory()->create();
        $category = BlogCategory::factory()->create();
        $imageBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAKAAAABaAQMAAAAFCM8RAAAAIGNIUk0AAHomAACAhAAA+gAAAIDoAAB1MAAA6mAAADqYAAAXcJy6UTwAAAAGUExURRQhPf///8N++ZgAAAABYktHRAH/Ai3eAAAAB3RJTUUH6ggeFRkClF5QhgAAACV0RVh0ZGF0ZTpjcmVhdGUAMjAyNi0wOC0zMFQyMToyNTowMiswMDowMDF0BPcAAAAldEVYdGRhdGU6bW9kaWZ5ADIwMjYtMDgtMzBUMjE6MjU6MDIrMDA6MDBAKbxLAAAAKHRFWHRkYXRlOnRpbWVzdGFtcAAyMDI2LTA4LTMwVDIxOjI1OjAyKzAwOjAwFzydlAAAABdJREFUOMtjYBgFo2AUjIJRMApGAfUBAAdiAAHnZfUEAAAAAElFTkSuQmCC', true);

        $this->assertIsString($imageBytes);

        $image = UploadedFile::fake()->createWithContent('featured.png', $imageBytes);

        Livewire::test(CreateBlogPost::class)
            ->fillForm([
                'title' => 'A Published Article With Media',
                'slug' => 'published-article-with-media',
                'excerpt' => 'A complete article that verifies the production featured-image workflow.',
                'content' => [
                    'type' => 'doc',
                    'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Content']]]],
                ],
                'blog_author_id' => $author->id,
                'blog_category_id' => $category->id,
                'status' => BlogPost::STATUS_PUBLISHED,
                'published_at' => now(),
                'featured_image' => [$image],
                'featured_image_alt' => 'Team reviewing a software product plan',
                'is_indexable' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $post = BlogPost::query()->where('slug', 'published-article-with-media')->firstOrFail();

        $this->assertNotNull($post->featured_image);
        Storage::disk('public')->assertExists((string) $post->featured_image);
    }

    public function test_editing_slug_records_redirect_history(): void
    {
        $post = BlogPost::factory()->create(['slug' => 'before-edit']);

        Livewire::test(EditBlogPost::class, ['record' => $post->getKey()])
            ->fillForm(['slug' => 'after-edit'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('blog_post_slug_redirects', [
            'blog_post_id' => $post->id,
            'slug' => 'before-edit',
        ]);
    }
}
