<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BlogAuthor;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Support\ServiceCatalog;
use Illuminate\Http\Response;

final class SitemapController extends Controller
{
    /**
     * Dynamic XML sitemap. Static marketing routes + every service detail page.
     */
    public function __invoke(): Response
    {
        /** @var array<int, array{loc:string, lastmod?:string}> $urls */
        $urls = [
            ['loc' => route('home')],
            ['loc' => route('services')],
            ['loc' => route('portfolio')],
            ['loc' => route('content-creation')],
            ['loc' => route('performance-marketing')],
            ['loc' => route('about')],
            ['loc' => route('why-us')],
            ['loc' => route('blog.index')],
        ];

        foreach (ServiceCatalog::slugs() as $slug) {
            $urls[] = ['loc' => route('services.show', $slug)];
        }

        BlogPost::query()
            ->published()
            ->where('is_indexable', true)
            ->orderBy('id')
            ->get(['slug', 'updated_at'])
            ->each(function (BlogPost $post) use (&$urls): void {
                $urls[] = [
                    'loc' => route('blog.show', $post->slug),
                    'lastmod' => $post->updated_at->toAtomString(),
                ];
            });

        BlogCategory::query()
            ->where('is_active', true)
            ->whereHas('posts', fn ($query) => $query->published(), '>=', 2)
            ->withMax(['posts as latest_post_update' => fn ($query) => $query->published()], 'updated_at')
            ->get()
            ->each(function (BlogCategory $category) use (&$urls): void {
                $urls[] = [
                    'loc' => route('blog.category', $category->slug),
                    'lastmod' => $category->latest_post_update
                        ? date(DATE_ATOM, strtotime((string) $category->latest_post_update))
                        : $category->updated_at->toAtomString(),
                ];
            });

        BlogAuthor::query()
            ->where('is_active', true)
            ->whereHas('posts', fn ($query) => $query->published(), '>=', 2)
            ->withMax(['posts as latest_post_update' => fn ($query) => $query->published()], 'updated_at')
            ->get()
            ->each(function (BlogAuthor $author) use (&$urls): void {
                $urls[] = [
                    'loc' => route('blog.author', $author->slug),
                    'lastmod' => $author->latest_post_update
                        ? date(DATE_ATOM, strtotime((string) $author->latest_post_update))
                        : $author->updated_at->toAtomString(),
                ];
            });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.e($u['loc'])."</loc>\n";
            if (isset($u['lastmod'])) {
                $xml .= '    <lastmod>'.e($u['lastmod'])."</lastmod>\n";
            }
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
