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
        ['no' => '001', 'name' => 'Helio Banking', 'client' => 'Series-B Fintech', 'cat' => 'SaaS Platform', 'year' => '2025', 'span' => 'lg:col-span-2 lg:row-span-2', 'stops' => ['#18d2ff', '#5c7cf5'], 'note' => 'Multi-tenant banking ops, 11 weeks to launch.'],
        ['no' => '002', 'name' => 'Atlas Studio', 'client' => 'Design Tooling', 'cat' => 'Web App', 'year' => '2025', 'span' => '', 'stops' => ['#5c7cf5', '#7b41b3'], 'note' => 'Real-time vector editor.'],
        ['no' => '003', 'name' => 'Lumen AI', 'client' => 'Healthcare LLM', 'cat' => 'AI Platform', 'year' => '2024', 'span' => 'lg:row-span-2', 'stops' => ['#7b41b3', '#c8459b'], 'note' => 'RAG over 2M clinical docs, sub-second latency.'],
        ['no' => '004', 'name' => 'Folio Commerce', 'client' => 'D2C Fashion', 'cat' => 'eCommerce', 'year' => '2024', 'span' => '', 'stops' => ['#c8459b', '#ff7ab8'], 'note' => 'Headless storefront, edge-rendered.'],
        ['no' => '005', 'name' => 'Beacon Analytics', 'client' => 'Marketing SaaS', 'cat' => 'Data Platform', 'year' => '2024', 'span' => 'lg:col-span-2', 'stops' => ['#ff7ab8', '#ef4444'], 'note' => 'Event pipeline + dashboard suite.'],
        ['no' => '006', 'name' => 'Forge Auth', 'client' => 'Internal Tool', 'cat' => 'Identity', 'year' => '2023', 'span' => '', 'stops' => ['#18d2ff', '#c8459b'], 'note' => 'Passkeys + SSO drop-in service.'],
        ['no' => '007', 'name' => 'Cipher Vault', 'client' => 'Legal AI', 'cat' => 'AI Platform', 'year' => '2023', 'span' => 'lg:col-span-2', 'stops' => ['#7b41b3', '#18d2ff'], 'note' => 'Redaction + retrieval over case law.'],
    ];

    $categories = ['All', 'SaaS Platform', 'AI Platform', 'Web App', 'eCommerce', 'Identity', 'Data Platform'];
@endphp

@section('content')
    {{-- ═══ HERO — full-bleed marquee of project titles ═══ --}}
    <section class="relative bg-[#06010f] text-white overflow-hidden grain pt-32 lg:pt-40 pb-10">
        <div class="container-vw mb-10">
            <div class="flex items-center gap-3 mb-5" data-reveal>
                <span class="h-px w-10 bg-white/30"></span>
                <span class="eyebrow !text-primary-300">Curated Index</span>
                <span class="text-[10px] font-mono uppercase tracking-[0.22em] text-white/35">{{ count($projects) }} of 67
                    shown</span>
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
    <section class="relative bg-[#06010f] text-white border-b border-white/10" x-data="{ view: 'grid', filter: 'All' }">
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
                    <article x-show="filter === 'All' || filter === '{{ $p['cat'] }}'" x-transition.opacity.duration.300ms
                        data-stagger-item
                        class="group relative rounded-2xl overflow-hidden border border-white/[0.08] hover:border-white/25 transition-all duration-500 {{ $p['span'] }}"
                        style="background: radial-gradient(at top left, {{ $p['stops'][0] }}30 0%, transparent 55%), radial-gradient(at bottom right, {{ $p['stops'][1] }}35 0%, transparent 60%), #0d0420;">

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
                    </article>
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
                        <a href="#"
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
@endsection