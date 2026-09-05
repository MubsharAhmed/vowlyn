{{-- ============================================================================
PORTFOLIO — "Mood Wall"
Distinctive concept: a curator's catalogue. View-toggle (Grid ↔ Index) flips
the entire layout between a visual mood-wall (asymmetric bento with hover
parallax) and a typographic index list. Massive marquee of project titles
above the wall, sticky filter chips by domain.
============================================================================ --}}
@extends('layouts.app')

@section('title', 'Portfolio — Vowlyn')
@section('description', 'A curated catalogue of shipped work — products, platforms, and experiences from the Vowlyn studio.')

@php
    $projects = [
        ['no' => '001', 'name' => 'Moventra Distribution', 'client' => 'Global Wholesale', 'cat' => 'B2B Commerce', 'year' => '2026', 'span' => 'lg:col-span-2 lg:row-span-2', 'stops' => ['#18d2ff', '#5c7cf5'], 'note' => 'International electronics distribution and inventory access.', 'url' => 'https://www.moventradistribution.com/', 'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?auto=format&fit=crop&w=1600&q=85', 'alt' => 'Electronics and accessories representing the Moventra Distribution project'],
        ['no' => '002', 'name' => 'Meljori Jewellery', 'client' => 'Fine Jewellery', 'cat' => 'eCommerce', 'year' => '2026', 'span' => '', 'stops' => ['#5c7cf5', '#7b41b3'], 'note' => 'A polished storefront for effortless product discovery.', 'url' => 'https://www.meljorijewellery.ca/', 'image' => 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?auto=format&fit=crop&w=1200&q=85', 'alt' => 'Fine jewellery representing the Meljori Jewellery project'],
        ['no' => '003', 'name' => 'IT Bridges', 'client' => 'Technology Services', 'cat' => 'Service Website', 'year' => '2026', 'span' => 'lg:row-span-2', 'stops' => ['#7b41b3', '#c8459b'], 'note' => 'Complex IT services made clear, credible, and actionable.', 'url' => 'https://itbridges.ca/', 'image' => 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=1200&q=85', 'alt' => 'Software development workspace representing the IT Bridges project'],
        ['no' => '004', 'name' => 'Persian Designer Rugs', 'client' => 'Designer Rugs', 'cat' => 'eCommerce', 'year' => '2026', 'span' => '', 'stops' => ['#c8459b', '#ff7ab8'], 'note' => 'An elegant digital catalogue for distinctive rug collections.', 'url' => 'https://persiandesignerrugs.ca/', 'image' => 'https://images.unsplash.com/photo-1600166898405-da9535204843?auto=format&fit=crop&w=1200&q=85', 'alt' => 'Patterned interior rug representing the Persian Designer Rugs project'],
        ['no' => '005', 'name' => 'Arian Rugs', 'client' => 'Home & Interiors', 'cat' => 'eCommerce', 'year' => '2025', 'span' => 'lg:col-span-2', 'stops' => ['#ff7ab8', '#ef4444'], 'note' => 'Commerce, catalogue UX, and organic growth in one platform.', 'url' => 'https://arianrugs.com/', 'image' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1600&q=85', 'alt' => 'Refined home interior representing the Arian Rugs project'],
        ['no' => '006', 'name' => 'Burloak Painting', 'client' => 'Local Services', 'cat' => 'Service Website', 'year' => '2025', 'span' => '', 'stops' => ['#18d2ff', '#c8459b'], 'note' => 'A lead-focused website built for local service discovery.', 'url' => 'https://burlingtonspainters.com/', 'image' => 'https://images.unsplash.com/photo-1562259949-e8e7689d7828?auto=format&fit=crop&w=1200&q=85', 'alt' => 'Professional painter representing the Burloak Painting project'],
    ];

    $categories = ['All', 'B2B Commerce', 'eCommerce', 'Service Website'];
@endphp

@section('content')
    {{-- ═══ HERO — full-bleed marquee of project titles ═══ --}}
    <section class="relative bg-brand-stage text-white overflow-hidden grain pt-32 lg:pt-40 pb-10">
        <div class="container-vw mb-10">
            <div class="flex items-center gap-3 mb-5" data-reveal>
                <span class="h-px w-10 bg-white/30"></span>
                <span class="eyebrow !text-primary-300">Curated Index</span>
                <span class="text-[10px] font-mono uppercase tracking-[0.22em] text-white/35">{{ count($projects) }} selected projects</span>
            </div>
            <h1 class="headline-display text-5xl sm:text-6xl lg:text-7xl xl:text-[5.5rem] text-white leading-[0.95]"
                data-reveal data-reveal-delay="0.05">
                Things we built<br />
                <span class="text-brand-gradient">on purpose.</span>
            </h1>
        </div>

        {{-- Marquee band of titles — bleeds edge to edge --}}
        <div class="marquee-lane py-3 border-y border-white/10 bg-white/[0.02]" data-reveal data-reveal-delay="0.12">
            <div class="marquee-track gap-12 lg:gap-16" data-marquee-speed="slow">
                @for ($pass = 0; $pass < 2; $pass++)
                    @foreach ($projects as $p)
                        <span class="inline-flex items-center gap-12 lg:gap-16 shrink-0">
                            <span
                                class="font-display font-bold text-3xl sm:text-5xl lg:text-6xl text-white/75 whitespace-nowrap">{{ $p['name'] }}</span>
                            <span aria-hidden="true" class="text-white/15 font-display text-3xl">✦</span>
                        </span>
                    @endforeach
                @endfor
            </div>
        </div>
    </section>

    {{-- ═══ TOOLBAR — filter + view toggle ═══ --}}
    <section class="relative bg-brand-stage text-white border-b border-white/10" x-data="{ view: 'grid', filter: 'All' }">
        <div class="container-vw py-5 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-2 flex-wrap">
                @foreach ($categories as $cat)
                    <button type="button" @click="filter = '{{ $cat }}'"
                        :class="filter === '{{ $cat }}' ? 'bg-white text-[#06010f] border-white' : 'text-white/65 border-white/12 hover:text-white hover:border-white/30'"
                        class="text-[11px] font-mono uppercase tracking-[0.16em] px-3 py-1.5 rounded-full border transition-colors">
                        {{ $cat }}
                    </button>
                @endforeach
            </div>

            <div class="inline-flex items-center gap-1 p-1 rounded-full border border-white/10 bg-white/[0.03]">
                <button type="button" @click="view = 'grid'"
                    :class="view === 'grid' ? 'bg-white text-[#06010f]' : 'text-white/60 hover:text-white'"
                    class="inline-flex items-center gap-1.5 text-[10px] font-mono uppercase tracking-[0.16em] px-3 py-1.5 rounded-full transition-colors">
                    <i data-lucide="layout-grid" class="size-3.5"></i> Grid
                </button>
                <button type="button" @click="view = 'index'"
                    :class="view === 'index' ? 'bg-white text-[#06010f]' : 'text-white/60 hover:text-white'"
                    class="inline-flex items-center gap-1.5 text-[10px] font-mono uppercase tracking-[0.16em] px-3 py-1.5 rounded-full transition-colors">
                    <i data-lucide="list" class="size-3.5"></i> Index
                </button>
            </div>
        </div>

        {{-- ═══ MOOD WALL (Grid view) ═══ --}}
        <div x-show="view === 'grid'" x-transition.opacity.duration.300ms class="container-vw py-12 lg:py-16">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-5 auto-rows-[18rem]">
                @foreach ($projects as $p)
                    <a href="{{ $p['url'] }}" target="_blank" rel="noopener noreferrer"
                        x-show="filter === 'All' || filter === '{{ $p['cat'] }}'" x-transition.opacity.duration.300ms
                        data-stagger-item
                        class="group relative rounded-2xl overflow-hidden border border-white/[0.08] hover:border-white/25 transition-all duration-500 {{ $p['span'] }}"
                        aria-label="View {{ $p['name'] }} website"
                        style="background: radial-gradient(at top left, {{ $p['stops'][0] }}30 0%, transparent 55%), radial-gradient(at bottom right, {{ $p['stops'][1] }}35 0%, transparent 60%), #0d0420;">

                        {{-- Project image with a branded colour wash for readable type. --}}
                        <img src="{{ $p['image'] }}" alt="{{ $p['alt'] }}"
                            loading="eager" decoding="async" width="1600" height="1100"
                            class="absolute inset-0 size-full object-cover opacity-70 saturate-[0.82] transition duration-700 ease-out group-hover:scale-[1.04] group-hover:opacity-80" />
                        <div aria-hidden="true"
                            class="absolute inset-0 bg-gradient-to-t from-[#0d0420] via-[#0d0420]/65 to-[#0d0420]/10"></div>
                        <div aria-hidden="true" class="absolute inset-0 opacity-45 mix-blend-color"
                            style="background: linear-gradient(135deg, {{ $p['stops'][0] }} 0%, transparent 48%, {{ $p['stops'][1] }} 100%);"></div>

                        <div aria-hidden="true"
                            class="absolute inset-0 plate-grid opacity-[0.07] group-hover:opacity-[0.14] transition-opacity duration-700">
                        </div>

                        {{-- Corner marks --}}
                        <span aria-hidden="true"
                            class="absolute top-3 left-3 size-2.5 border-t border-l border-white/25"></span>
                        <span aria-hidden="true"
                            class="absolute top-3 right-3 size-2.5 border-t border-r border-white/25"></span>
                        <span aria-hidden="true"
                            class="absolute bottom-3 left-3 size-2.5 border-b border-l border-white/25"></span>
                        <span aria-hidden="true"
                            class="absolute bottom-3 right-3 size-2.5 border-b border-r border-white/25"></span>

                        <div class="relative h-full p-6 lg:p-7 flex flex-col">
                            {{-- Top: number + year --}}
                            <header class="flex items-center justify-between mb-auto">
                                <span
                                    class="text-[10px] font-mono uppercase tracking-[0.22em] text-white/45">PRJ.{{ $p['no'] }}</span>
                                <span
                                    class="text-[10px] font-mono uppercase tracking-[0.16em] text-white/45">{{ $p['year'] }}</span>
                            </header>

                            {{-- Middle: name (scales by span) --}}
                            <div class="my-6">
                                <p class="text-[10px] font-mono uppercase tracking-[0.18em] text-white/45 mb-2">{{ $p['cat'] }}
                                    · {{ $p['client'] }}</p>
                                <h3 class="font-display font-bold leading-[0.95] tracking-tight text-white text-3xl sm:text-4xl lg:text-[2.5rem]"
                                    style="background: linear-gradient(135deg, #ffffff 0%, #ffffff 55%, {{ $p['stops'][1] }} 100%); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">
                                    {{ $p['name'] }}
                                </h3>
                            </div>

                            {{-- Bottom: note + cta --}}
                            <footer class="flex items-end justify-between gap-4">
                                <p class="text-sm text-white/65 leading-snug max-w-xs">{{ $p['note'] }}</p>
                                <span
                                    class="inline-flex shrink-0 items-center justify-center size-10 rounded-full border border-white/15 text-white group-hover:bg-white group-hover:text-[#06010f] transition-colors">
                                    <i data-lucide="arrow-up-right"
                                        class="size-4 transition-transform group-hover:rotate-45"></i>
                                </span>
                            </footer>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ═══ INDEX LIST (Index view) ═══ --}}
        <div x-show="view === 'index'" x-cloak x-transition.opacity.duration.300ms class="container-vw py-12 lg:py-16">
            <ul class="divide-y divide-white/10 border-y border-white/10">
                {{-- Header row --}}
                <li
                    class="hidden md:grid grid-cols-[80px_1fr_1.2fr_140px_60px] gap-4 py-3 text-[10px] font-mono uppercase tracking-[0.22em] text-white/40">
                    <span>No.</span>
                    <span>Project</span>
                    <span>Client · Category</span>
                    <span>Year</span>
                    <span></span>
                </li>
                @foreach ($projects as $p)
                    <li x-show="filter === 'All' || filter === '{{ $p['cat'] }}'" x-transition.opacity.duration.300ms
                        class="group">
                        <a href="{{ $p['url'] }}" target="_blank" rel="noopener noreferrer"
                            class="grid grid-cols-[80px_1fr] md:grid-cols-[80px_1fr_1.2fr_140px_60px] gap-4 items-center py-6 lg:py-7 transition-colors hover:bg-white/[0.03] -mx-4 px-4 rounded-xl">
                            <span class="text-[10px] font-mono uppercase tracking-[0.22em] text-white/45">{{ $p['no'] }}</span>
                            <h3 class="font-display text-2xl lg:text-3xl text-white leading-tight truncate"
                                style="background: linear-gradient(120deg, #ffffff 0%, #ffffff 60%, {{ $p['stops'][1] }} 100%); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">
                                {{ $p['name'] }}
                            </h3>
                            <span
                                class="hidden md:block text-sm text-white/65 font-mono uppercase tracking-[0.14em] truncate">{{ $p['client'] }}
                                · {{ $p['cat'] }}</span>
                            <span
                                class="hidden md:block text-sm font-mono uppercase tracking-[0.16em] text-white/55 tabular-nums">{{ $p['year'] }}</span>
                            <span class="hidden md:inline-flex justify-end">
                                <i data-lucide="arrow-up-right"
                                    class="size-4 text-white/50 transition-transform group-hover:rotate-45 group-hover:text-white"></i>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- ═══ PROOF IN NUMBERS — count-up metrics + brand marquee ═══ --}}
    @php
        $metrics = [
            ['v' => '120', 'suffix' => '+', 'dec' => 0, 'label' => 'Products shipped'],
            ['v' => '6',   'suffix' => '',  'dec' => 0, 'label' => 'Featured brands'],
            ['v' => '99.9','suffix' => '%', 'dec' => 1, 'label' => 'Avg. uptime'],
            ['v' => '10',  'suffix' => 'x', 'dec' => 0, 'label' => 'Peak organic growth'],
        ];
        $proofBrands = ['Moventra Distribution', 'Meljori Jewellery', 'IT Bridges', 'Persian Designer Rugs', 'Arian Rugs', 'Burloak Painting'];
    @endphp
    <section class="relative bg-mesh-light section-vw overflow-hidden">
        <div class="container-vw">
            <div class="grid lg:grid-cols-[1fr_auto] gap-8 items-end mb-14 lg:mb-20">
                <div>
                    <span class="eyebrow !text-primary-700" data-reveal>Proof in numbers</span>
                    <h2 class="headline-display text-4xl lg:text-5xl mt-3 max-w-xl" data-reveal data-reveal-delay="0.05">
                        Outcomes we can put<br/>a number on.
                    </h2>
                </div>
                <p class="text-on-surface-muted max-w-sm leading-relaxed" data-reveal data-reveal-delay="0.1">
                    A focused selection of six client brands — measured in shipped product, not slide decks.
                </p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-px rounded-3xl overflow-hidden border border-on-surface/10 bg-on-surface/5" data-stagger="0.1">
                @foreach ($metrics as $m)
                    <div data-stagger-item class="bg-white px-6 py-9 lg:py-12 flex flex-col">
                        <div class="flex items-baseline font-display text-5xl lg:text-6xl font-black tabular-nums leading-none text-primary-700">
                            <span data-counter="{{ $m['v'] }}" @if($m['dec']) data-counter-decimals="{{ $m['dec'] }}" @endif>0</span>
                            <span>{{ $m['suffix'] }}</span>
                        </div>
                        <p class="mt-4 text-[11px] font-mono uppercase tracking-[0.18em] text-on-surface-muted">{{ $m['label'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Brand marquee --}}
            <div class="mt-16 lg:mt-20" data-reveal>
                <p class="text-center text-[10px] font-mono uppercase tracking-[0.24em] text-on-surface-muted mb-8">Trusted across the brands we direct</p>
                <div class="marquee-lane">
                    <div class="marquee-track gap-12 font-display text-2xl lg:text-3xl font-semibold text-on-surface/30">
                        @for ($i = 0; $i < 2; $i++)
                            @foreach ($proofBrands as $b)
                                <span class="flex items-center gap-12">
                                    {{ $b }}
                                    <span class="inline-block size-2 rounded-full bg-gradient-to-br from-primary-500 to-blush-500"></span>
                                </span>
                            @endforeach
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ FEATURED CASE STUDY — editorial spotlight w/ parallax ═══ --}}
    <section class="relative bg-brand-stage text-white overflow-hidden grain section-vw" data-parallax-group>
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 -left-24 size-[30rem] rounded-full bg-primary-700/30 blur-[120px]"></div>
            <div class="absolute -bottom-24 -right-16 size-[26rem] rounded-full bg-blush-500/20 blur-[120px]"></div>
        </div>

        <div class="container-vw relative grid lg:grid-cols-[1fr_1.05fr] gap-12 lg:gap-16 items-center">
            {{-- Copy --}}
            <div>
                <div class="flex items-center gap-3 mb-6" data-reveal>
                    <span class="eyebrow !text-primary-300">Featured case study</span>
                    <span class="h-px w-12 bg-white/20"></span>
                    <span class="text-[10px] font-mono uppercase tracking-[0.22em] text-white/40">eCommerce · Growth</span>
                </div>
                <h2 class="headline-display text-4xl lg:text-5xl xl:text-6xl" data-reveal data-reveal-delay="0.05">
                    A catalogue built<br/><span class="text-brand-gradient">to convert.</span>
                </h2>
                <p class="text-white/65 text-lg mt-6 max-w-lg leading-relaxed" data-reveal data-reveal-delay="0.1">
                    Arian Rugs brings catalogue UX, e-commerce, and organic discovery into one focused platform —
                    helping customers move from inspiration to the right piece without friction.
                </p>

                <div class="mt-8 grid grid-cols-3 gap-4 max-w-md" data-stagger="0.1">
                    @php
                        $caseStats = [
                            ['v' => '38', 'suffix' => '%', 'dec' => 0, 'l' => 'Conversion lift'],
                            ['v' => '10', 'suffix' => 'x', 'dec' => 0, 'l' => 'Organic growth'],
                            ['v' => '5',  'suffix' => 'yr', 'dec' => 0, 'l' => 'Platform uptime'],
                        ];
                    @endphp
                    @foreach ($caseStats as $c)
                        <div data-stagger-item class="border-l border-white/15 pl-4">
                            <div class="flex items-baseline font-display text-3xl font-bold tabular-nums leading-none">
                                <span data-counter="{{ $c['v'] }}">0</span><span>{{ $c['suffix'] }}</span>
                            </div>
                            <p class="mt-2 text-[10px] font-mono uppercase tracking-[0.16em] text-white/45">{{ $c['l'] }}</p>
                        </div>
                    @endforeach
                </div>

                <a href="https://arianrugs.com/" target="_blank" rel="noopener noreferrer" data-magnetic="0.2" class="mt-10 inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-white text-[#0d0420] font-medium hover:bg-white/90 transition-colors" data-reveal data-reveal-delay="0.15">
                    View Arian Rugs <i data-lucide="arrow-up-right" class="size-4"></i>
                </a>
            </div>

            {{-- Mockup w/ parallax --}}
            <div class="relative" data-reveal data-reveal-delay="0.1">
                <div class="relative rounded-2xl overflow-hidden border border-white/12 shadow-[0_50px_120px_-40px_rgba(0,0,0,0.7)]" data-tilt>
                    {{-- browser chrome --}}
                    <div class="flex items-center gap-2 px-4 py-3 bg-[#160828]/90 border-b border-white/10">
                        <span class="size-2.5 rounded-full bg-blush-400/80"></span>
                        <span class="size-2.5 rounded-full bg-amber-300/80"></span>
                        <span class="size-2.5 rounded-full bg-emerald-400/80"></span>
                        <span class="ml-3 text-[10px] font-mono text-white/40 tracking-wider">arianrugs.com</span>
                    </div>
                    <div class="relative aspect-[4/3] overflow-hidden bg-[#0d0420]">
                        <img src="https://images.unsplash.com/photo-1600166898405-da9535204843?auto=format&fit=crop&w=1400&q=80"
                             alt="Arian Rugs e-commerce catalogue"
                             loading="lazy" width="1400" height="1050"
                             class="absolute inset-0 w-full h-full object-cover" data-parallax="0.1" />
                        <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-t from-[#0d0420]/60 via-transparent to-transparent"></div>
                    </div>
                </div>
                {{-- floating quote chip --}}
                <div class="absolute -bottom-6 -left-4 lg:-left-8 max-w-xs rounded-2xl bg-white text-on-surface p-5 shadow-2xl rotate-[-2deg]">
                    <p class="text-sm leading-snug italic">"Our store rebuild lifted conversions 38% in the first quarter."</p>
                    <p class="mt-2 text-[10px] font-mono uppercase tracking-[0.18em] text-on-surface-muted">— Daniel Arian, Founder</p>
                </div>
            </div>
        </div>
    </section>
@endsection
