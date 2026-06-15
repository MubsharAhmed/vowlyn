<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <meta name="theme-color" content="#0b0b12" />
    <meta name="color-scheme" content="light dark" />

    <title>@yield('title', 'Vowlyn — We Build Digital Products That Scale')</title>
    <meta name="description"
        content="@yield('description', 'Vowlyn is a premium software company building modern web platforms, mobile apps, AI-powered solutions, and scalable SaaS systems that drive real growth.')" />

    {{-- Open Graph --}}
    <meta property="og:type" content="website" />
    <meta property="og:title" content="@yield('title', 'Vowlyn — We Build Digital Products That Scale')" />
    <meta property="og:description"
        content="@yield('description', 'Premium software company for modern digital growth.')" />
    <meta property="og:image" content="{{ asset('brand/vowlyn-logo.png') }}" />

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('brand/vowlyn-logo.png') }}" />

    {{-- Preconnect to Bunny Fonts (privacy-friendly, GDPR) --}}
    <link rel="preconnect" href="https://fonts.bunny.net" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
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