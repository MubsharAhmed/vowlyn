<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Response;

final class BlogFeedController extends Controller
{
    public function __invoke(): Response
    {
        $posts = BlogPost::query()
            ->published()
            ->where('is_indexable', true)
            ->with(['author', 'category'])
            ->latest('published_at')
            ->limit(20)
            ->get();

        return response()
            ->view('feeds.blog', compact('posts'))
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
