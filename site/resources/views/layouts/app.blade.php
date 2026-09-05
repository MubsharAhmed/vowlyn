<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

@php
    /* ---------------------------------------------------------------------
       SEO meta resolution (SEO/AEO/GEO rewrite, 2026)
       Pages may @section any of: title, description, canonical, og_type,
       og_title, og_description, twitter_title, twitter_description.
       Unset values cascade down to sensible site-wide defaults.
    --------------------------------------------------------------------- */
    /* Normalise yielded content: decode any entities so the Blade {{ }} echo
       below escapes exactly once (avoids &amp;amp; double-encoding). */
    $dec = fn (string $v): string => html_entity_decode($v, ENT_QUOTES);
    $metaTitle = $dec($__env->yieldContent('title'))
        ?: 'Custom Software Development Studio | Vowlyn';
    $metaDescription = $dec($__env->yieldContent('description'))
        ?: 'Vowlyn is a custom software development studio building web apps, mobile apps, AI features, and scalable SaaS. A small, senior team that ships fast — and you own the code. Start a project.';
    $canonicalUrl = $__env->yieldContent('canonical') ?: url()->current();
    $robots = $__env->yieldContent('robots') ?: 'index,follow,max-image-preview:large';
    $ogType = $__env->yieldContent('og_type') ?: 'website';
    $ogTitle = $dec($__env->yieldContent('og_title')) ?: $metaTitle;
    $ogDescription = $dec($__env->yieldContent('og_description')) ?: $metaDescription;
    $twitterTitle = $dec($__env->yieldContent('twitter_title')) ?: $ogTitle;
    $twitterDescription = $dec($__env->yieldContent('twitter_description')) ?: $ogDescription;
    $ogImage = $__env->yieldContent('og_image') ?: asset('brand/vowlyn-logo.png');
    $twitterImage = $__env->yieldContent('twitter_image') ?: $ogImage;
    $ogImageWidth = $__env->yieldContent('og_image_width');
    $ogImageHeight = $__env->yieldContent('og_image_height');
@endphp

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <meta name="theme-color" content="#0b0b12" />
    <meta name="color-scheme" content="light dark" />

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}" />
    <meta name="robots" content="{{ $robots }}" />
    <link rel="canonical" href="{{ $canonicalUrl }}" />
    <link rel="alternate" type="application/rss+xml" title="Vowlyn Journal" href="{{ route('blog.feed') }}" />

    {{-- Open Graph --}}
    <meta property="og:type" content="{{ $ogType }}" />
    <meta property="og:url" content="{{ $canonicalUrl }}" />
    <meta property="og:site_name" content="Vowlyn" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:title" content="{{ $ogTitle }}" />
    <meta property="og:description" content="{{ $ogDescription }}" />
    <meta property="og:image" content="{{ $ogImage }}" />
    <meta property="og:image:alt" content="{{ $ogTitle }}" />
    @if ($ogImageWidth && $ogImageHeight)
        <meta property="og:image:width" content="{{ $ogImageWidth }}" />
        <meta property="og:image:height" content="{{ $ogImageHeight }}" />
    @endif

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $twitterTitle }}" />
    <meta name="twitter:description" content="{{ $twitterDescription }}" />
    <meta name="twitter:image" content="{{ $twitterImage }}" />
    <meta name="twitter:image:alt" content="{{ $twitterTitle }}" />

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('brand/vowlyn-logo.png') }}" />

    {{-- Preconnect to Bunny Fonts (privacy-friendly, GDPR) --}}
    <link rel="preconnect" href="https://fonts.bunny.net" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Per-page structured data (JSON-LD) & extra head tags --}}
    @stack('head')
</head>

<body class="bg-mesh-light grain antialiased text-on-surface min-h-dvh flex flex-col overflow-x-hidden">

    {{-- Skip link for a11y --}}
    <a href="#main"
        class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-primary-700 focus:text-white focus:rounded-full">
        Skip to content
    </a>

    @include('partials.nav')

    <main id="main" class="flex-1 relative isolate">
        @yield('content')
    </main>

    @include('partials.footer')

</body>

</html>
