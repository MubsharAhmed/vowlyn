{{-- ============================================================================
MARKETPLACE — "Trading Floor"
Distinctive concept: a Bloomberg-terminal-meets-product-catalog. A live ticker
tape scrolls product names + price deltas at the top; below sits a
filter-driven bento of product cards each with a live sparkline + add-to-cart
quick action. Sticky sidebar filter on desktop, Alpine.js powers filtering.
============================================================================ --}}
@extends('layouts.app')

@section('title', 'Software Templates, Kits & Components — Vowlyn Marketplace')
@section('description', 'Buy production-ready software templates, UI kits, and components from the Vowlyn studio — SaaS boilerplates, auth, billing, dashboards & CMS. License and ship the same day.')
@section('canonical', 'https://vowlyn.com/marketplace')
@section('og_title', 'Software Templates, Kits & Components — Vowlyn Marketplace')
@section('og_description', 'Production-ready templates, UI kits & components — SaaS boilerplates, auth, billing & more. License in a click.')
@section('twitter_title', 'Software Templates, Kits & Components — Vowlyn Marketplace')
@section('twitter_description', 'Production-ready templates, UI kits & components from the Vowlyn studio. License in a click.')

@push('head')
    {{-- CollectionPage + ItemList of Products — eligible for product-rich results --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": "CollectionPage",
      "name": "Vowlyn Marketplace — software templates, kits & components",
      "url": "https://vowlyn.com/marketplace",
      "about": { "@id": "https://vowlyn.com/#organization" },
      "mainEntity": {
        "@type": "ItemList",
        "name": "Production-ready software products",
        "itemListElement": [
          { "@type": "Product", "name": "Hyperframes", "category": "Editorial · Motion", "offers": { "@type": "Offer", "price": "129", "priceCurrency": "USD", "availability": "https://schema.org/InStock" } },
          { "@type": "Product", "name": "Pulse Kit", "category": "Dashboard · Glass", "offers": { "@type": "Offer", "price": "79", "priceCurrency": "USD", "availability": "https://schema.org/InStock" } },
          { "@type": "Product", "name": "Forge Auth", "category": "SSO · Passkeys", "offers": { "@type": "Offer", "price": "199", "priceCurrency": "USD", "availability": "https://schema.org/InStock" } },
          { "@type": "Product", "name": "Beacon Analytics", "category": "Event Pipeline · OLAP", "offers": { "@type": "Offer", "price": "249", "priceCurrency": "USD", "availability": "https://schema.org/InStock" } },
          { "@type": "Product", "name": "Vault Billing", "category": "Stripe · Metered", "offers": { "@type": "Offer", "price": "179", "priceCurrency": "USD", "availability": "https://schema.org/InStock" } },
          { "@type": "Product", "name": "Echo CMS", "category": "Headless · Edge", "offers": { "@type": "Offer", "price": "159", "priceCurrency": "USD", "availability": "https://schema.org/InStock" } },
          { "@type": "Product", "name": "Atelier UI", "category": "Design System", "offers": { "@type": "Offer", "price": "99", "priceCurrency": "USD", "availability": "https://schema.org/InStock" } },
          { "@type": "Product", "name": "Orbit Charts", "category": "D3 · Canvas", "offers": { "@type": "Offer", "price": "89", "priceCurrency": "USD", "availability": "https://schema.org/InStock" } },
          { "@type": "Product", "name": "Cipher Vault", "category": "RAG · Embeddings", "offers": { "@type": "Offer", "price": "299", "priceCurrency": "USD", "availability": "https://schema.org/InStock" } }
        ]
      }
    }
    </script>
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        { "@type": "Question", "name": "What can I buy on the Vowlyn Marketplace?", "acceptedAnswer": { "@type": "Answer", "text": "Production-ready software templates, UI kits, and full components, including authentication, billing, analytics, CMS, dashboards, and design systems. Each ships with a live demo and one-click licensing, priced from $79 to $299." } },
        { "@type": "Question", "name": "How does licensing work?", "acceptedAnswer": { "@type": "Answer", "text": "Transparent one-time or team licensing with an instant invoice, no sales calls and no per-seat games. Pull the repo, add your keys, and deploy the same day." } },
        { "@type": "Question", "name": "Is there a bundle for all products?", "acceptedAnswer": { "@type": "Answer", "text": "Yes. The Studio Bundle unlocks all 67 products under one license, with lifetime updates and team seats included." } },
        { "@type": "Question", "name": "Can I sell my own templates on Vowlyn?", "acceptedAnswer": { "@type": "Answer", "text": "Yes. List templates, components, and full apps to a vetted audience of founders and teams. You keep the IP and the majority of every sale while Vowlyn handles distribution, licensing, and payouts." } }
      ]
    }
    </script>
@endpush

@php
    $tickers = [
        ['Hyperframes', '+12.4'], ['Pulse Kit', '+3.1'], ['Echo CMS', '-1.2'], ['Atelier UI', '+8.7'],
        ['Forge Auth', '+0.6'], ['Beacon Analytics', '+22.0'], ['Vault Billing', '+4.3'], ['Loom Studio', '-0.4'],
        ['Cipher Vault', '+9.8'], ['Relay Webhooks', '+1.7'], ['Orbit Charts', '+15.5'], ['Glyph Icons', '+0.9'],
    ];

    $categories = ['All', 'UI Kits', 'Backends', 'AI', 'Auth', 'Data', 'Billing'];

    $products = [
        ['name' => 'Hyperframes', 'cat' => 'UI Kits', 'price' => '$129', 'delta' => '+12.4', 'tag' => 'Editorial · Motion', 'blurb' => 'Editorial, motion-rich page frames for marketing sites.', 'spark' => 'M0,30 L20,28 L40,24 L60,18 L80,20 L100,10 L120,14 L140,6', 'stops' => ['#18d2ff', '#5c7cf5']],
        ['name' => 'Pulse Kit', 'cat' => 'UI Kits', 'price' => '$79', 'delta' => '+3.1', 'tag' => 'Dashboard · Glass', 'blurb' => 'A glassmorphic dashboard UI kit, ready to theme.', 'spark' => 'M0,22 L20,20 L40,24 L60,18 L80,14 L100,16 L120,10 L140,12', 'stops' => ['#5c7cf5', '#7b41b3']],
        ['name' => 'Forge Auth', 'cat' => 'Auth', 'price' => '$199', 'delta' => '+0.6', 'tag' => 'SSO · Passkeys', 'blurb' => 'Drop-in SSO and passkey authentication.', 'spark' => 'M0,18 L20,18 L40,16 L60,17 L80,14 L100,16 L120,12 L140,14', 'stops' => ['#7b41b3', '#c8459b']],
        ['name' => 'Beacon Analytics', 'cat' => 'Data', 'price' => '$249', 'delta' => '+22.0', 'tag' => 'Event Pipeline · OLAP', 'blurb' => 'Event pipeline plus an OLAP dashboard suite.', 'spark' => 'M0,38 L20,34 L40,28 L60,22 L80,18 L100,10 L120,8 L140,4', 'stops' => ['#c8459b', '#ff7ab8']],
        ['name' => 'Vault Billing', 'cat' => 'Billing', 'price' => '$179', 'delta' => '+4.3', 'tag' => 'Stripe · Metered', 'blurb' => 'Stripe-powered metered and subscription billing.', 'spark' => 'M0,24 L20,22 L40,20 L60,18 L80,14 L100,12 L120,10 L140,8', 'stops' => ['#ff7ab8', '#ef4444']],
        ['name' => 'Echo CMS', 'cat' => 'Backends', 'price' => '$159', 'delta' => '-1.2', 'tag' => 'Headless · Edge', 'blurb' => 'Headless, edge-rendered content management.', 'spark' => 'M0,16 L20,18 L40,14 L60,18 L80,20 L100,16 L120,22 L140,18', 'stops' => ['#18d2ff', '#c8459b']],
        ['name' => 'Atelier UI', 'cat' => 'UI Kits', 'price' => '$99', 'delta' => '+8.7', 'tag' => 'Design System', 'blurb' => 'A complete, tokenized design system.', 'spark' => 'M0,28 L20,26 L40,22 L60,20 L80,14 L100,12 L120,8 L140,6', 'stops' => ['#5c7cf5', '#18d2ff']],
        ['name' => 'Orbit Charts', 'cat' => 'Data', 'price' => '$89', 'delta' => '+15.5', 'tag' => 'D3 · Canvas', 'blurb' => 'D3 + Canvas charting components.', 'spark' => 'M0,34 L20,30 L40,26 L60,18 L80,22 L100,14 L120,16 L140,8', 'stops' => ['#7b41b3', '#5c7cf5']],
        ['name' => 'Cipher Vault', 'cat' => 'AI', 'price' => '$299', 'delta' => '+9.8', 'tag' => 'RAG · Embeddings', 'blurb' => 'RAG and embeddings starter for AI search.', 'spark' => 'M0,30 L20,28 L40,22 L60,20 L80,16 L100,12 L120,10 L140,6', 'stops' => ['#c8459b', '#7b41b3']],
    ];
@endphp

@section('content')
    {{-- ═══ TICKER TAPE — top of page, live readout ═══ --}}
    <div class="bg-brand-stage border-b border-white/10 pt-24 lg:pt-28">
        <div class="marquee-lane py-3 border-y border-white/5 bg-white/[0.02]">
            <div class="marquee-track gap-10" data-marquee-speed="medium">
                @for ($pass = 0; $pass < 2; $pass++)
                    @foreach ($tickers as [$name, $delta])
                        @php $up = !str_starts_with($delta, '-'); @endphp
                        <span class="inline-flex items-center gap-4 shrink-0 font-mono text-sm uppercase tracking-wider">
                            <span class="text-white/85">{{ $name }}</span>
                            <span class="inline-flex items-center gap-1 {{ $up ? 'text-emerald-400' : 'text-red-400' }}">
                                <i data-lucide="{{ $up ? 'arrow-up-right' : 'arrow-down-right' }}" class="size-3.5"></i>
                                {{ $delta }}%
                            </span>
                            <span aria-hidden="true" class="text-white/15">•</span>
                        </span>
                    @endforeach
                @endfor
            </div>
        </div>
    </div>

    {{-- ═══ HERO ═══ --}}
    <section class="relative bg-brand-stage text-white overflow-hidden grain py-16 lg:py-24">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 -right-20 size-[26rem] rounded-full bg-blush-500/25 blur-3xl"></div>
            <div class="absolute bottom-0 -left-32 size-[28rem] rounded-full bg-primary-700/30 blur-3xl"></div>
        </div>

        <div class="container-vw relative">
            <div class="flex items-center gap-3 mb-6" data-reveal>
                <span class="size-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="eyebrow !text-emerald-300">Trading Floor — Open</span>
                <span class="text-[10px] font-mono uppercase tracking-[0.22em] text-white/35">{{ count($products) }} listings</span>
            </div>

            <h1 class="headline-display text-5xl sm:text-6xl lg:text-7xl xl:text-[5.5rem] text-white leading-[0.95]" data-reveal data-reveal-delay="0.05">
                A <span class="text-brand-gradient">curated marketplace</span><br/>
                for studio-grade software.
            </h1>

            {{-- Intro — names the product types (fixes thin content; SEO rewrite, 2026) --}}
            <p class="mt-7 text-lg lg:text-xl text-white/65 max-w-3xl leading-relaxed" data-reveal data-reveal-delay="0.1">
                The Vowlyn Marketplace offers production-ready
                <strong class="font-semibold text-white">software templates, UI kits, and components</strong> —
                from SaaS boilerplates and authentication to billing, dashboards, analytics, and CMS. Every listing
                is founder-built, vetted, and ships with a live demo. License in one click, pull the repo, drop in
                your keys, and deploy the same day. No sales calls, no seat games.
            </p>

            {{-- Quick Answer (GEO) --}}
            <x-quick-answer :dark="true" class="mt-8 max-w-2xl" data-reveal data-reveal-delay="0.14">
                The Vowlyn Marketplace sells production-ready software templates, UI kits, and components —
                including SaaS boilerplates, authentication, billing, analytics, dashboards, and CMS — priced from
                $79 to $299. Each listing is founder-built, ships with a live demo, and is licensed in one click
                for same-day deployment. A Studio Bundle unlocks all 67 products under one license.
            </x-quick-answer>

            <div class="mt-10 grid sm:grid-cols-3 gap-px overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] max-w-3xl" data-reveal data-reveal-delay="0.18">
                @foreach ([['67', 'Active Listings'], ['12k', 'Production Installs'], ['98%', 'Renewal Rate']] as [$v, $l])
                    <div class="bg-[#0d0420]/60 backdrop-blur-xl px-5 py-5 flex flex-col">
                        <span class="font-display text-3xl font-bold text-white tabular-nums leading-none">{{ $v }}</span>
                        <span class="mt-2 text-[10px] font-mono uppercase tracking-[0.2em] text-white/45">{{ $l }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══ MARKETPLACE FLOOR — filter sidebar + product bento ═══ --}}
    <section class="relative bg-brand-stage text-white py-14 lg:py-20" x-data="{ filter: 'All', search: '' }">
        <div class="container-vw">

            <div class="grid lg:grid-cols-[260px_1fr] gap-8 lg:gap-12">

                {{-- ─── Sticky filter sidebar ─── --}}
                <aside class="lg:sticky lg:top-28 lg:self-start space-y-6">
                    <div>
                        <p class="text-[10px] font-mono uppercase tracking-[0.22em] text-white/45 mb-3">Search</p>
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-white/40"></i>
                            <input x-model="search" type="search" placeholder="Find a product…"
                                   class="w-full pl-10 pr-3 py-2.5 rounded-xl bg-white/[0.04] border border-white/10 text-sm text-white placeholder:text-white/30 focus:outline-none focus:border-primary-400 transition-colors"/>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-mono uppercase tracking-[0.22em] text-white/45 mb-3">Category</p>
                        <ul class="space-y-1">
                            @foreach ($categories as $cat)
                                <li>
                                    <button type="button"
                                            @click="filter = '{{ $cat }}'"
                                            :class="filter === '{{ $cat }}' ? 'bg-white/10 text-white border-white/20' : 'text-white/60 border-transparent hover:text-white hover:bg-white/[0.04]'"
                                            class="w-full text-left px-3 py-2 rounded-lg text-sm border transition-colors flex items-center justify-between">
                                        <span>{{ $cat }}</span>
                                        <i data-lucide="arrow-right" class="size-3.5 opacity-0 -translate-x-1" :class="filter === '{{ $cat }}' && 'opacity-100 translate-x-0'" style="transition: all .3s"></i>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="rounded-xl p-4 bg-white/[0.03] border border-white/10">
                        <p class="text-[10px] font-mono uppercase tracking-[0.18em] text-white/45 mb-1">Studio Bundle</p>
                        <p class="font-display text-xl text-white leading-tight mb-2">All 67 products, one licence.</p>
                        <p class="text-xs text-white/55 mb-3">Lifetime updates · team seats included.</p>
                        <a href="#bundle" class="text-xs font-mono uppercase tracking-[0.16em] text-primary-300 inline-flex items-center gap-1">View bundle <i data-lucide="arrow-up-right" class="size-3.5"></i></a>
                    </div>
                </aside>

                {{-- ─── Product grid ─── --}}
                <div>
                    {{-- Section heading — fixes H1 → H3 hierarchy skip (SEO rewrite, 2026) --}}
                    <h2 class="headline-display text-3xl sm:text-4xl text-white mb-6" data-reveal>
                        Production-ready <span class="text-brand-gradient">templates, kits, and components.</span>
                    </h2>

                    {{-- Toolbar --}}
                    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
                        <p class="text-[10px] font-mono uppercase tracking-[0.22em] text-white/50">
                            Showing <span class="text-white">{{ count($products) }}</span> · sorted by traction
                        </p>
                        <div class="flex items-center gap-2 text-[10px] font-mono uppercase tracking-[0.16em] text-white/50">
                            <span class="size-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Live price feed
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4 lg:gap-5">
                        @foreach ($products as $i => $p)
                            @php $gid = 'mkt-' . $i; $up = !str_starts_with($p['delta'], '-'); @endphp
                            <article x-show="(filter === 'All' || filter === '{{ $p['cat'] }}') && (search === '' || '{{ strtolower($p['name']) }}'.includes(search.toLowerCase()))"
                                     x-transition.opacity.duration.300ms
                                     data-stagger-item
                                     class="group relative rounded-2xl p-5 bg-white/[0.03] border border-white/[0.08] hover:border-white/20 hover:bg-white/[0.06] transition-all duration-500 overflow-hidden flex flex-col">

                                {{-- Top gradient hairline --}}
                                <div aria-hidden="true" class="absolute inset-x-0 top-0 h-px opacity-50 group-hover:opacity-100 transition-opacity duration-500"
                                     style="background: linear-gradient(90deg, transparent, {{ $p['stops'][1] }}, transparent);"></div>

                                {{-- Header row --}}
                                <div class="flex items-start justify-between gap-3 mb-4">
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-mono uppercase tracking-[0.16em] text-white/45 mb-1">{{ $p['tag'] }}</p>
                                        <h3 class="font-display text-xl lg:text-2xl font-bold text-white leading-tight truncate">{{ $p['name'] }}</h3>
                                        <p class="mt-1.5 text-xs text-white/55 leading-relaxed">{{ $p['blurb'] }}</p>
                                    </div>
                                    <span class="inline-flex items-center gap-1 text-[10px] font-mono uppercase tracking-[0.14em] px-2 py-0.5 rounded-full {{ $up ? 'bg-emerald-400/12 text-emerald-300 border-emerald-400/20' : 'bg-red-400/10 text-red-300 border-red-400/20' }} border shrink-0">
                                        <i data-lucide="{{ $up ? 'arrow-up-right' : 'arrow-down-right' }}" class="size-2.5"></i>
                                        {{ $p['delta'] }}%
                                    </span>
                                </div>

                                {{-- Mini sparkline --}}
                                <svg viewBox="0 0 140 40" class="w-full h-12 mb-4" preserveAspectRatio="none" aria-hidden="true">
                                    <defs>
                                        <linearGradient id="{{ $gid }}-stroke" x1="0" x2="1" y1="0" y2="0">
                                            <stop offset="0%" stop-color="{{ $p['stops'][0] }}"/>
                                            <stop offset="100%" stop-color="{{ $p['stops'][1] }}"/>
                                        </linearGradient>
                                        <linearGradient id="{{ $gid }}-fill" x1="0" x2="0" y1="0" y2="1">
                                            <stop offset="0%" stop-color="{{ $p['stops'][0] }}" stop-opacity="0.3"/>
                                            <stop offset="100%" stop-color="{{ $p['stops'][1] }}" stop-opacity="0"/>
                                        </linearGradient>
                                    </defs>
                                    <path d="{{ $p['spark'] }} L140,40 L0,40 Z" fill="url(#{{ $gid }}-fill)" data-sparkline-area/>
                                    <path d="{{ $p['spark'] }}" fill="none" stroke="url(#{{ $gid }}-stroke)" stroke-width="1.5" stroke-linecap="round" data-sparkline-stroke/>
                                </svg>

                                {{-- Footer: price + buy --}}
                                <div class="mt-auto flex items-end justify-between gap-3 pt-3 border-t border-white/8">
                                    <div>
                                        <p class="text-[10px] font-mono uppercase tracking-[0.16em] text-white/40">Lifetime</p>
                                        <p class="font-display text-2xl font-bold text-white tabular-nums">{{ $p['price'] }}</p>
                                    </div>
                                    <a href="#" class="inline-flex items-center gap-1.5 text-[11px] font-mono uppercase tracking-[0.18em] px-3 py-2 rounded-full bg-white text-[#06010f] hover:bg-white/90 transition-colors">
                                        Add <i data-lucide="plus" class="size-3"></i>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ HOW BUYING WORKS — 3-step thread ═══ --}}
    @php
        $steps = [
            ['no' => '01', 'icon' => 'search',       'title' => 'Browse the floor', 'desc' => 'Filter by stack, license and use-case. Every listing is vetted and ships with a live demo.'],
            ['no' => '02', 'icon' => 'badge-check',   'title' => 'License in a click','desc' => 'Transparent one-time or team licensing. Instant invoice, no sales calls, no seat games.'],
            ['no' => '03', 'icon' => 'package-check', 'title' => 'Ship the same day', 'desc' => 'Pull the repo, drop in your keys, deploy. Founder-built code with docs that respect your time.'],
        ];
    @endphp
    <section class="relative bg-mesh-light section-vw overflow-hidden">
        <div class="container-vw">
            <div class="max-w-2xl mb-14 lg:mb-20">
                <span class="eyebrow !text-primary-700" data-reveal>How it works</span>
                <h2 class="headline-display text-4xl lg:text-5xl mt-3" data-reveal data-reveal-delay="0.05">
                    From cart to production<br/><span class="text-brand-gradient">in an afternoon.</span>
                </h2>
            </div>

            <div class="relative grid md:grid-cols-3 gap-8 lg:gap-6" data-stagger="0.12">
                {{-- Connecting thread (desktop) --}}
                <div aria-hidden="true" class="hidden md:block absolute top-7 left-[16%] right-[16%] h-px bg-gradient-to-r from-primary-300/40 via-blush-300/50 to-primary-300/40"></div>

                @foreach ($steps as $s)
                    <div data-stagger-item class="relative">
                        <div class="relative flex items-center gap-4 mb-6">
                            <span class="relative z-10 size-14 grid place-items-center rounded-2xl bg-white border border-on-surface/10 shadow-[0_12px_30px_-12px_rgba(75,0,130,0.25)]">
                                <i data-lucide="{{ $s['icon'] }}" class="size-6 text-primary-700"></i>
                            </span>
                            <span class="font-display text-5xl font-black text-primary-100 leading-none select-none">{{ $s['no'] }}</span>
                        </div>
                        <h3 class="font-display text-2xl font-semibold text-on-surface">{{ $s['title'] }}</h3>
                        <p class="mt-3 text-on-surface-muted leading-relaxed">{{ $s['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══ STUDIO BUNDLE — the high-value offer, surfaced clearly ═══ --}}
    <section id="bundle" class="relative bg-surface section-vw overflow-hidden">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-24 -right-24 size-[26rem] rounded-full bg-primary-300/30 blur-3xl"></div>
            <div class="absolute bottom-0 -left-24 size-[24rem] rounded-full bg-blush-200/40 blur-3xl"></div>
        </div>

        <div class="container-vw relative">
            <div class="rounded-3xl border border-primary-200/70 bg-gradient-to-br from-white via-lavender-50 to-blush-50 p-8 lg:p-14 shadow-[0_40px_90px_-40px_rgba(75,0,130,0.35)] grid lg:grid-cols-[1.3fr_1fr] gap-10 items-center" data-reveal>
                <div>
                    <span class="eyebrow !text-primary-700">Best value</span>
                    <h2 class="headline-display text-4xl sm:text-5xl mt-4 text-slate-900">
                        Get all 67 products with<br/><span class="text-brand-gradient">the Studio Bundle.</span>
                    </h2>
                    <p class="mt-6 text-lg text-on-surface-muted leading-relaxed max-w-xl">
                        One license unlocks the entire Vowlyn catalog — every template, kit, and component — with
                        <strong class="font-semibold text-on-surface">lifetime updates</strong> and
                        <strong class="font-semibold text-on-surface">team seats included</strong>. Best value for
                        studios and teams shipping more than one product a year.
                    </p>
                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <a href="{{ url('/#contact') }}" data-magnetic="0.2" class="btn btn-primary !py-3.5 !px-7">
                            Get the Studio Bundle
                            <i data-lucide="arrow-up-right" class="size-4"></i>
                        </a>
                        <span class="text-sm font-mono uppercase tracking-[0.16em] text-on-surface/50">One license · lifetime updates</span>
                    </div>
                </div>

                <ul class="space-y-4">
                    @foreach ([
                        ['layers', 'Entire catalog', 'Every template, kit & component — today and future releases.'],
                        ['refresh-ccw', 'Lifetime updates', 'Every improvement ships to your license, forever.'],
                        ['users', 'Team seats included', 'One invoice for the whole studio — no per-seat games.'],
                    ] as [$icon, $t, $d])
                        <li class="flex items-start gap-4 rounded-2xl bg-white/80 border border-lavender-300/70 p-5">
                            <span class="grid place-items-center size-11 rounded-xl bg-primary-50 text-primary-700 border border-primary-200/60 shrink-0">
                                <i data-lucide="{{ $icon }}" class="size-5"></i>
                            </span>
                            <div>
                                <p class="font-display text-lg font-semibold text-slate-900">{{ $t }}</p>
                                <p class="text-sm text-on-surface-muted mt-0.5">{{ $d }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- ═══ MARKETPLACE FAQ + KEY TAKEAWAYS (AEO) ═══ --}}
    @php
        $marketFaqs = [
            [
                'q' => 'What can I buy on the Vowlyn Marketplace?',
                'a' => 'Production-ready software templates, UI kits, and full components — including authentication, billing, analytics, CMS, dashboards, and design systems — each with a live demo and one-click licensing. Prices range from $79 to $299.',
            ],
            [
                'q' => 'How does licensing work?',
                'a' => 'Transparent one-time or team licensing with an instant invoice — no sales calls and no per-seat games. Pull the repo, add your keys, and deploy the same day.',
            ],
            [
                'q' => 'Is there a bundle for all products?',
                'a' => 'Yes. The Studio Bundle unlocks all 67 products under one license, with lifetime updates and team seats included.',
            ],
            [
                'q' => 'Can I sell my own templates on Vowlyn?',
                'a' => 'Yes. List templates, components, and full apps to a vetted audience of founders and teams. You keep the IP and the majority of every sale while Vowlyn handles distribution, licensing, and payouts.',
            ],
        ];
    @endphp
    <section aria-labelledby="paa-marketplace" class="section-vw bg-mesh-light relative overflow-hidden">
        <div class="container-vw relative">
            <div class="grid lg:grid-cols-[0.8fr_1.2fr] gap-10 lg:gap-16 items-start">
                <div data-reveal>
                    <span class="eyebrow !text-primary-700">People also ask</span>
                    <h2 id="paa-marketplace" class="headline-display text-3xl lg:text-4xl mt-3">Marketplace FAQ.</h2>
                    <p class="text-on-surface-muted mt-4 leading-relaxed">
                        Buying, licensing, bundling, or selling — the short answers live here.
                    </p>

                    <x-key-takeaways class="mt-8" :items="[
                        'Buy production-ready software templates, UI kits, and components ($79–$299).',
                        'Categories: SaaS boilerplates, auth, billing, analytics, dashboards, CMS, design systems.',
                        'One-click licensing, live demo on every listing, same-day deployment.',
                        'Studio Bundle unlocks all 67 products with lifetime updates and team seats.',
                        'Sellers keep the IP and the majority of every sale.',
                    ]" />
                </div>

                <x-faq-accordion :items="$marketFaqs" />
            </div>
        </div>
    </section>

    {{-- ═══ SELL ON VOWLYN — counters + CTA band ═══ --}}
    <section class="relative bg-brand-stage text-white overflow-hidden grain section-vw">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-20 left-1/3 size-[28rem] rounded-full bg-blush-500/20 blur-[120px]"></div>
            <div class="absolute bottom-0 right-0 size-[24rem] rounded-full bg-primary-700/30 blur-[120px]"></div>
        </div>

        <div class="container-vw relative grid lg:grid-cols-[1.1fr_1fr] gap-12 lg:gap-20 items-center">
            {{-- Left: pitch --}}
            <div>
                <span class="eyebrow !text-primary-300" data-reveal>For builders</span>
                <h2 class="headline-display text-4xl sm:text-5xl lg:text-6xl mt-4" data-reveal data-reveal-delay="0.05">
                    Built something good?<br/><span class="text-brand-gradient">Put it on the floor.</span>
                </h2>
                <p class="text-white/65 text-lg mt-6 max-w-lg leading-relaxed" data-reveal data-reveal-delay="0.1">
                    List your templates, components and full apps to a vetted audience of founders and teams.
                    You keep the IP and the lion's share of every sale — we handle distribution, licensing and payouts.
                </p>
                <div class="mt-9 flex flex-wrap items-center gap-4" data-reveal data-reveal-delay="0.15">
                    <a href="{{ url('/#contact') }}" data-magnetic="0.22" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-white text-[#0d0420] font-medium hover:bg-white/90 transition-colors">
                        Apply to sell <i data-lucide="arrow-up-right" class="size-4"></i>
                    </a>
                    <span class="text-sm font-mono uppercase tracking-[0.18em] text-white/45">Rolling applications · vetted weekly</span>
                </div>
            </div>

            {{-- Right: animated stat tiles --}}
            <div class="grid grid-cols-2 gap-4 lg:gap-5" data-stagger="0.1">
                @php
                    $sellerStats = [
                        ['v' => '85', 'suffix' => '%', 'dec' => 0, 'label' => 'Seller payout share'],
                        ['v' => '48', 'suffix' => 'h', 'dec' => 0, 'label' => 'Avg. review time'],
                        ['v' => '12', 'suffix' => 'k', 'dec' => 0, 'label' => 'Buyers on the floor'],
                        ['v' => '4.9', 'suffix' => '', 'dec' => 1, 'label' => 'Avg. seller rating'],
                    ];
                @endphp
                @foreach ($sellerStats as $s)
                    <div data-stagger-item class="rounded-2xl p-6 border border-white/10 bg-[#160828]/60 backdrop-blur-xl">
                        <div class="flex items-baseline font-display text-4xl lg:text-5xl font-bold tabular-nums leading-none">
                            <span data-counter="{{ $s['v'] }}" @if($s['dec']) data-counter-decimals="{{ $s['dec'] }}" @endif class="text-brand-gradient">0</span>
                            <span class="text-brand-gradient">{{ $s['suffix'] }}</span>
                        </div>
                        <p class="mt-3 text-[11px] font-mono uppercase tracking-[0.18em] text-white/45">{{ $s['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
