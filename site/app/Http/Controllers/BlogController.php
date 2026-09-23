<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BlogAuthor;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogPostSlugRedirect;
use App\Services\BlogContentService;
use App\Support\ServiceCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class BlogController extends Controller
{
    public function __construct(private readonly BlogContentService $contentService) {}

    public function index(): View
    {
        $featured = BlogPost::query()
            ->published()
            ->where('is_featured', true)
            ->with(['author', 'category'])
            ->latest('published_at')
            ->first();

        $posts = BlogPost::query()
            ->published()
            ->when($featured, fn ($query) => $query->whereKeyNot($featured->getKey()))
            ->with(['author', 'category'])
            ->latest('published_at')
            ->paginate(9);

        $categories = BlogCategory::query()
            ->where('is_active', true)
            ->whereHas('posts', fn ($query) => $query->published())
            ->withCount(['posts as published_posts_count' => fn ($query) => $query->published()])
            ->orderBy('name')
            ->get();

        // The featured note is pinned above the grid, so it sits outside the paginator.
        $totalPublished = $posts->total() + ($featured ? 1 : 0);

        return view('pages.blog.index', compact('featured', 'posts', 'categories', 'totalPublished'));
    }

    public function subscribe(): View
    {
        return view('pages.blog.subscribe');
    }

    public function category(string $category): View
    {
        $category = BlogCategory::query()
            ->where('slug', $category)
            ->where('is_active', true)
            ->firstOrFail();

        $posts = $category->posts()
            ->published()
            ->with(['author', 'category'])
            ->latest('published_at')
            ->paginate(9);

        $categories = BlogCategory::query()
            ->where('is_active', true)
            ->whereHas('posts', fn ($query) => $query->published())
            ->withCount(['posts as published_posts_count' => fn ($query) => $query->published()])
            ->orderBy('name')
            ->get();

        return view('pages.blog.category', compact('category', 'posts', 'categories'));
    }

    public function author(string $author): View
    {
        $author = BlogAuthor::query()
            ->where('slug', $author)
            ->where('is_active', true)
            ->firstOrFail();

        $posts = $author->posts()
            ->published()
            ->with(['author', 'category'])
            ->latest('published_at')
            ->paginate(9);

        return view('pages.blog.author', compact('author', 'posts'));
    }

    public function show(string $slug): View|RedirectResponse
    {
        $post = BlogPost::query()
            ->published()
            ->where('slug', $slug)
            ->with(['author', 'category'])
            ->first();

        if (! $post) {
            $redirect = BlogPostSlugRedirect::query()
                ->where('slug', $slug)
                ->whereHas('post', fn ($query) => $query->published())
                ->with('post')
                ->first();

            if ($redirect?->post) {
                return redirect()->route('blog.show', $redirect->post->slug, 301);
            }

            abort(404);
        }

        return $this->renderPost($post, false);
    }

    public function preview(BlogPost $post): View
    {
        $post->loadMissing(['author', 'category']);

        return $this->renderPost($post, true);
    }

    private function renderPost(BlogPost $post, bool $preview): View
    {
        $prepared = $this->contentService->prepare($post);
        $relatedPosts = BlogPost::query()
            ->published()
            ->whereKeyNot($post->getKey())
            ->where('blog_category_id', $post->blog_category_id)
            ->with(['author', 'category'])
            ->latest('published_at')
            ->limit(3)
            ->get();
        $relatedService = $post->category->related_service_slug
            ? ServiceCatalog::find($post->category->related_service_slug)
            : null;

        return view('pages.blog.show', [
            'post' => $post,
            'content' => $prepared['html'],
            'toc' => $prepared['toc'],
            'readingMinutes' => $prepared['reading_minutes'],
            'relatedPosts' => $relatedPosts,
            'relatedService' => $relatedService,
            'preview' => $preview,
        ]);
    }
}
