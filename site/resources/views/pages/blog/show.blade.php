@extends('layouts.app')

@section('title', $post->resolvedSeoTitle())
@section('description', $post->resolvedSeoDescription())
@section('canonical', $preview ? route('blog.show', $post->slug) : $post->canonicalUrl())
@section('robots', $preview || ! $post->is_indexable ? 'noindex,nofollow' : 'index,follow,max-image-preview:large')
@section('og_type', 'article')
@section('og_title', $post->resolvedSocialTitle())
@section('og_description', $post->resolvedSocialDescription())
@section('og_image', $post->imageUrl('social'))
@section('og_image_width', '1200')
@section('og_image_height', '630')
@section('twitter_title', $post->resolvedSocialTitle())
@section('twitter_description', $post->resolvedSocialDescription())
@section('twitter_image', $post->imageUrl('social'))

@php
    $articleSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $post->title,
        'description' => $post->resolvedSeoDescription(),
        'url' => route('blog.show', $post->slug),
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => route('blog.show', $post->slug)],
        'image' => [$post->imageUrl('square'), $post->imageUrl('landscape'), $post->imageUrl('social')],
        'datePublished' => $post->published_at?->toIso8601String(),
        'dateModified' => $post->updated_at->toIso8601String(),
        'articleSection' => $post->category->name,
        'keywords' => implode(', ', $post->tags ?? []),
        'author' => [
            '@type' => 'Person',
            'name' => $post->author->name,
            'url' => route('blog.author', $post->author->slug),
            'sameAs' => array_values($post->author->same_as ?? []),
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => 'Vowlyn',
            'url' => route('home'),
            'logo' => ['@type' => 'ImageObject', 'url' => asset('brand/vowlyn-logo.png')],
        ],
    ];
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Journal', 'item' => route('blog.index')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $post->category->name, 'item' => route('blog.category', $post->category->slug)],
            ['@type' => 'ListItem', 'position' => 4, 'name' => $post->title, 'item' => route('blog.show', $post->slug)],
        ],
    ];
@endphp

@push('head')
    <meta property="article:published_time" content="{{ $post->published_at?->toIso8601String() }}" />
    <meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}" />
    <meta property="article:author" content="{{ route('blog.author', $post->author->slug) }}" />
    <meta property="article:section" content="{{ $post->category->name }}" />
    @foreach ($post->tags ?? [] as $tag)<meta property="article:tag" content="{{ $tag }}" />@endforeach
    <script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endpush

@section('content')
    <article class="journal-article">
        @if ($preview)
            <div class="journal-preview-banner">Preview mode — this private link expires automatically and the page cannot be indexed.</div>
        @endif
        <header class="journal-article__hero">
            <div class="journal-grid" aria-hidden="true"></div>
            <div class="container-vw relative">
                <nav aria-label="Breadcrumb" class="journal-breadcrumb">
                    <a href="{{ route('home') }}">Home</a><span>/</span>
                    <a href="{{ route('blog.index') }}">Journal</a><span>/</span>
                    <a href="{{ route('blog.category', $post->category->slug) }}">{{ $post->category->name }}</a>
                </nav>
                <div class="journal-article__heading">
                    <div class="journal-article__kicker">
                        <span>{{ $post->category->name }}</span>
                        <span>{{ str_pad((string) $readingMinutes, 2, '0', STR_PAD_LEFT) }} min read</span>
                    </div>
                    <h1>{{ $post->title }}</h1>
                    <p>{{ $post->excerpt }}</p>
                    <div class="journal-article__byline">
                        <a href="{{ route('blog.author', $post->author->slug) }}" class="journal-article__author">
                            @if ($post->author->avatar_url)
                                <img src="{{ $post->author->avatar_url }}" alt="" width="48" height="48" />
                            @else
                                <span aria-hidden="true">{{ mb_substr($post->author->name, 0, 1) }}</span>
                            @endif
                            <span><strong>{{ $post->author->name }}</strong><small>{{ $post->author->job_title ?: 'Vowlyn studio' }}</small></span>
                        </a>
                        <div>
                            <span>Published <time datetime="{{ $post->published_at?->toIso8601String() }}">{{ $post->published_at?->format('F j, Y') ?: 'Not published' }}</time></span>
                            @if ($post->updated_at->diffInDays($post->published_at ?? $post->created_at) > 0)
                                <span>Updated <time datetime="{{ $post->updated_at->toIso8601String() }}">{{ $post->updated_at->format('F j, Y') }}</time></span>
                            @endif
                        </div>
                    </div>
                </div>
                @if ($post->featured_image)
                    @php($heroDimensions = $post->imageDimensions('hero'))
                    <figure class="journal-article__cover">
                        <img src="{{ $post->imageUrl('hero') }}" alt="{{ $post->featured_image_alt }}" width="{{ $heroDimensions['width'] }}" height="{{ $heroDimensions['height'] }}" fetchpriority="high" decoding="async" />
                        @if ($post->featured_image_caption || $post->featured_image_credit)
                            <figcaption><span>{{ $post->featured_image_caption }}</span><cite>{{ $post->featured_image_credit }}</cite></figcaption>
                        @endif
                    </figure>
                @endif
            </div>
        </header>

        <div class="journal-article__body container-vw">
            <aside class="journal-article__rail">
                @if (count($toc) >= 2)
                    <nav aria-label="On this page" class="journal-toc">
                        <span>On this page</span>
                        @foreach ($toc as $item)
                            <a href="#{{ $item['id'] }}" @class(['is-sub' => $item['level'] === 3])>{{ $item['label'] }}</a>
                        @endforeach
                    </nav>
                @endif
                <div class="journal-share">
                    <span>Share</span>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('blog.show', $post->slug)) }}" target="_blank" rel="noopener noreferrer" aria-label="Share on LinkedIn">in</a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.show', $post->slug)) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener noreferrer" aria-label="Share on X">x</a>
                    <button type="button" data-copy-url="{{ route('blog.show', $post->slug) }}" aria-label="Copy article link">↗</button>
                </div>
            </aside>

            <div class="journal-prose">
                {{ $content }}

                @if ($relatedService)
                    <aside class="journal-service-cta">
                        <span>From insight to implementation</span>
                        <h2>Need {{ strtolower($relatedService['name']) }} that holds up in the real world?</h2>
                        <p>{{ $relatedService['meta_description'] }}</p>
                        <a href="{{ route('services.show', $relatedService['slug']) }}">Explore {{ $relatedService['name'] }} <span aria-hidden="true">↗</span></a>
                    </aside>
                @endif

                <footer class="journal-author-card">
                    <div class="journal-author-card__portrait">
                        @if ($post->author->avatar_url)<img src="{{ $post->author->avatar_url }}" alt="" width="128" height="128" />@else<span>{{ mb_substr($post->author->name, 0, 1) }}</span>@endif
                    </div>
                    <div><span>Written by</span><h2>{{ $post->author->name }}</h2><p>{{ $post->author->bio ?: 'Part of the Vowlyn team building useful digital products with care.' }}</p><a href="{{ route('blog.author', $post->author->slug) }}">More from this author →</a></div>
                </footer>
            </div>
        </div>

        @if ($relatedPosts->isNotEmpty())
            <section class="journal-related">
                <div class="container-vw">
                    <div class="journal-section-heading"><span>Continue reading</span><a href="{{ route('blog.category', $post->category->slug) }}">All {{ $post->category->name }} notes ↗</a></div>
                    <div class="journal-card-grid">@foreach ($relatedPosts as $relatedPost)<x-blog.post-card :post="$relatedPost" />@endforeach</div>
                </div>
            </section>
        @endif
    </article>
@endsection
