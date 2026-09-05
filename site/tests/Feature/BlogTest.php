<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\BlogAuthor;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

final class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_lists_only_posts_that_are_public_now(): void
    {
        $category = BlogCategory::factory()->create();
        $author = BlogAuthor::factory()->create();
        $published = BlogPost::factory()->published()->create([
            'blog_category_id' => $category->id,
            'blog_author_id' => $author->id,
            'title' => 'A published field note',
        ]);
        $draft = BlogPost::factory()->create([
            'blog_category_id' => $category->id,
            'blog_author_id' => $author->id,
            'title' => 'A private draft',
        ]);
        $scheduled = BlogPost::factory()->scheduled()->create([
            'blog_category_id' => $category->id,
            'blog_author_id' => $author->id,
            'title' => 'A future article',
        ]);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee($draft->title)
            ->assertDontSee($scheduled->title)
            ->assertSee(route('blog.subscribe'), false)
            ->assertSee('application/rss+xml', false);
    }

    public function test_readable_follow_page_explains_rss_and_keeps_the_raw_feed_available(): void
    {
        $this->get(route('blog.subscribe'))
            ->assertOk()
            ->assertSee('name="robots" content="noindex,follow"', false)
            ->assertSee('RSS quick start')
            ->assertSee('Copy feed address')
            ->assertSee('data-copy-url="'.route('blog.feed').'"', false)
            ->assertSee('open the raw XML feed');
    }

    public function test_article_outputs_complete_search_social_and_structured_metadata(): void
    {
        $author = BlogAuthor::factory()->create([
            'name' => 'Ayesha Khan',
            'slug' => 'ayesha-khan',
            'same_as' => ['LinkedIn' => 'https://www.linkedin.com/in/ayesha-khan'],
        ]);
        $category = BlogCategory::factory()->create(['name' => 'AI Engineering', 'slug' => 'ai-engineering']);
        $post = BlogPost::factory()->published()->create([
            'blog_author_id' => $author->id,
            'blog_category_id' => $category->id,
            'title' => 'How to Ship a Reliable AI Product',
            'slug' => 'ship-reliable-ai-product',
            'seo_title' => 'How to Ship a Reliable AI Product | Vowlyn',
            'seo_description' => 'A practical guide to designing, evaluating, and shipping useful AI products.',
            'social_title' => 'Reliable AI Products: A Field Guide',
            'tags' => ['AI', 'Product engineering'],
        ]);

        $response = $this->get(route('blog.show', $post->slug));

        $response->assertOk()
            ->assertSee('<title>How to Ship a Reliable AI Product | Vowlyn</title>', false)
            ->assertSee('name="robots" content="index,follow,max-image-preview:large"', false)
            ->assertSee('rel="canonical" href="'.route('blog.show', $post->slug).'"', false)
            ->assertSee('property="og:type" content="article"', false)
            ->assertSee('property="article:published_time"', false)
            ->assertSee('"@type":"BlogPosting"', false)
            ->assertSee('"@type":"BreadcrumbList"', false)
            ->assertSee('Ayesha Khan')
            ->assertSee('Published');
    }

    public function test_draft_is_not_public_but_can_be_viewed_with_a_signed_preview_link(): void
    {
        $post = BlogPost::factory()->create();

        $this->get(route('blog.show', $post->slug))->assertNotFound();

        $previewUrl = URL::temporarySignedRoute('blog.preview', now()->addMinutes(10), ['post' => $post]);
        $this->get($previewUrl)
            ->assertOk()
            ->assertSee('Preview mode')
            ->assertSee('name="robots" content="noindex,nofollow"', false);

        $this->get(route('blog.preview', $post))->assertForbidden();
    }

    public function test_changing_a_slug_creates_a_permanent_redirect(): void
    {
        $post = BlogPost::factory()->published()->create(['slug' => 'old-article-url']);
        $post->update(['slug' => 'new-article-url']);

        $this->get('/blog/old-article-url')
            ->assertRedirect('/blog/new-article-url')
            ->assertStatus(301);
    }

    public function test_sitemap_contains_only_indexable_published_posts_with_lastmod(): void
    {
        $published = BlogPost::factory()->published()->create(['slug' => 'indexable-article']);
        $hidden = BlogPost::factory()->published()->create(['slug' => 'hidden-article', 'is_indexable' => false]);
        $draft = BlogPost::factory()->create(['slug' => 'draft-article']);

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('blog.index'), false)
            ->assertSee(route('blog.show', $published->slug), false)
            ->assertSee('<lastmod>', false)
            ->assertDontSee(route('blog.show', $hidden->slug), false)
            ->assertDontSee(route('blog.show', $draft->slug), false)
            ->assertDontSee('<priority>', false)
            ->assertDontSee('<changefreq>', false);
    }

    public function test_feed_contains_recent_public_indexable_posts_only(): void
    {
        $published = BlogPost::factory()->published()->create(['title' => 'Included in RSS']);
        BlogPost::factory()->published()->create(['title' => 'Noindex article', 'is_indexable' => false]);
        BlogPost::factory()->create(['title' => 'Draft article']);

        $this->get(route('blog.feed'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->assertSee($published->title)
            ->assertDontSee('Noindex article')
            ->assertDontSee('Draft article');
    }

    public function test_thin_category_and_author_archives_are_noindex_until_they_have_two_posts(): void
    {
        $post = BlogPost::factory()->published()->create();

        $this->get(route('blog.category', $post->category->slug))
            ->assertOk()
            ->assertSee('name="robots" content="noindex,follow,max-image-preview:large"', false);

        $this->get(route('blog.author', $post->author->slug))
            ->assertOk()
            ->assertSee('name="robots" content="noindex,follow,max-image-preview:large"', false)
            ->assertSee('"@type":"ProfilePage"', false);
    }

    public function test_established_category_and_author_archives_are_indexable_and_in_the_sitemap(): void
    {
        $firstPost = BlogPost::factory()->published()->create();
        BlogPost::factory()->published()->create([
            'blog_author_id' => $firstPost->blog_author_id,
            'blog_category_id' => $firstPost->blog_category_id,
        ]);

        $categoryUrl = route('blog.category', $firstPost->category->slug);
        $authorUrl = route('blog.author', $firstPost->author->slug);

        $this->get($categoryUrl)
            ->assertOk()
            ->assertSee('name="robots" content="index,follow,max-image-preview:large"', false);

        $this->get($authorUrl)
            ->assertOk()
            ->assertSee('name="robots" content="index,follow,max-image-preview:large"', false);

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertSee($categoryUrl, false)
            ->assertSee($authorUrl, false);
    }

    public function test_rich_content_is_sanitized_before_rendering(): void
    {
        $post = BlogPost::factory()->published()->create([
            'content' => [
                'type' => 'doc',
                'content' => [[
                    'type' => 'paragraph',
                    'content' => [[
                        'type' => 'text',
                        'text' => '<script>alert("unsafe")</script>Useful guidance',
                    ]],
                ]],
            ],
        ]);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertDontSee('<script>alert("unsafe")</script>', false)
            ->assertSee('Useful guidance');
    }

    public function test_verified_client_can_open_blog_admin_resources(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)
            ->get('/admin/blog-posts')
            ->assertOk();

        $this->actingAs($user)
            ->get('/admin/blog-categories')
            ->assertOk();

        $this->actingAs($user)
            ->get('/admin/blog-authors')
            ->assertOk();
    }
}
