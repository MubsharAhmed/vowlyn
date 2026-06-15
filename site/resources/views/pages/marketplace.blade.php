{{-- ============================================================================
MARKETPLACE — "Trading Floor"
Distinctive concept: a Bloomberg-terminal-meets-product-catalog. A live ticker
tape scrolls product names + price deltas at the top; below sits a
filter-driven bento of product cards each with a live sparkline + add-to-cart
quick action. Sticky sidebar filter on desktop, Alpine.js powers filtering.
============================================================================ --}}
@extends('layouts.app')

@section('title', 'Marketplace — Vowlyn')
@section('description', 'Curated production-ready templates, kits, and components from the Vowlyn studio.')

@php
    $tickers = [
        ['Hyperframes', '+12.4'], ['Pulse Kit', '+3.1'], ['Echo CMS', '-1.2'], ['Atelier UI', '+8.7'],
        ['Forge Auth', '+0.6'], ['Beacon Analytics', '+22.0'], ['Vault Billing', '+4.3'], ['Loom Studio', '-0.4'],
        ['Cipher Vault', '+9.8'], ['Relay Webhooks', '+1.7'], ['Orbit Charts', '+15.5'], ['Glyph Icons', '+0.9'],
    ];

    $categories = ['All', 'UI Kits', 'Backends', 'AI', 'Auth', 'Data', 'Billing'];

    $products = [
        ['name' => 'Hyperframes', 'cat' => 'UI Kits', 'price' => '$129', 'delta' => '+12.4', 'tag' => 'Editorial · Motion', 'spark' => 'M0,30 L20,28 L40,24 L60,18 L80,20 L100,10 L120,14 L140,6', 'stops' => ['#18d2ff', '#5c7cf5']],
        ['name' => 'Pulse Kit', 'cat' => 'UI Kits', 'price' => '$79', 'delta' => '+3.1', 'tag' => 'Dashboard · Glass', 'spark' => 'M0,22 L20,20 L40,24 L60,18 L80,14 L100,16 L120,10 L140,12', 'stops' => ['#5c7cf5', '#7b41b3']],
        ['name' => 'Forge Auth', 'cat' => 'Auth', 'price' => '$199', 'delta' => '+0.6', 'tag' => 'SSO · Passkeys', 'spark' => 'M0,18 L20,18 L40,16 L60,17 L80,14 L100,16 L120,12 L140,14', 'stops' => ['#7b41b3', '#c8459b']],
        ['name' => 'Beacon Analytics', 'cat' => 'Data', 'price' => '$249', 'delta' => '+22.0', 'tag' => 'Event Pipeline · OLAP', 'spark' => 'M0,38 L20,34 L40,28 L60,22 L80,18 L100,10 L120,8 L140,4', 'stops' => ['#c8459b', '#ff7ab8']],
        ['name' => 'Vault Billing', 'cat' => 'Billing', 'price' => '$179', 'delta' => '+4.3', 'tag' => 'Stripe · Metered', 'spark' => 'M0,24 L20,22 L40,20 L60,18 L80,14 L100,12 L120,10 L140,8', 'stops' => ['#ff7ab8', '#ef4444']],
        ['name' => 'Echo CMS', 'cat' => 'Backends', 'price' => '$159', 'delta' => '-1.2', 'tag' => 'Headless · Edge', 'spark' => 'M0,16 L20,18 L40,14 L60,18 L80,20 L100,16 L120,22 L140,18', 'stops' => ['#18d2ff', '#c8459b']],
        ['name' => 'Atelier UI', 'cat' => 'UI Kits', 'price' => '$99', 'delta' => '+8.7', 'tag' => 'Design System', 'spark' => 'M0,28 L20,26 L40,22 L60,20 L80,14 L100,12 L120,8 L140,6', 'stops' => ['#5c7cf5', '#18d2ff']],
        ['name' => 'Orbit Charts', 'cat' => 'Data', 'price' => '$89', 'delta' => '+15.5', 'tag' => 'D3 · Canvas', 'spark' => 'M0,34 L20,30 L40,26 L60,18 L80,22 L100,14 L120,16 L140,8', 'stops' => ['#7b41b3', '#5c7cf5']],
        ['name' => 'Cipher Vault', 'cat' => 'AI', 'price' => '$299', 'delta' => '+9.8', 'tag' => 'RAG · Embeddings', 'spark' => 'M0,30 L20,28 L40,22 L60,20 L80,16 L100,12 L120,10 L140,6', 'stops' => ['#c8459b', '#7b41b3']],
    ];
@endphp

@section('content')
    {{-- ═══ TICKER TAPE — top of page, live readout ═══ --}}
    <div class="bg-[#06010f] border-b border-white/10 pt-24 lg:pt-28">
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

            <div class="mt-10 grid sm:grid-cols-3 gap-px overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] max-w-3xl" data-reveal data-reveal-delay="0.12">
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
    <section class="relative bg-[#06010f] text-white py-14 lg:py-20" x-data="{ filter: 'All', search: '' }">
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
                        <a href="#" class="text-xs font-mono uppercase tracking-[0.16em] text-primary-300 inline-flex items-center gap-1">View bundle <i data-lucide="arrow-up-right" class="size-3.5"></i></a>
                    </div>
                </aside>

                {{-- ─── Product grid ─── --}}
                <div>
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
@endsection
