{{-- ============================================================================
CONTENT CREATION — Editorial motion studio
Video-first showcase with interactive players and a campaign workflow.
============================================================================ --}}
@extends('layouts.app')

@section('title', 'Content Creation & Video Production — Vowlyn')
@section('description', 'Strategy-led video content, social campaigns, brand films, and vertical edits created by Vowlyn. Explore our latest work for Caledon Golf Club.')
@section('canonical', 'https://vowlyn.com/content-creation')
@section('og_title', 'Content Creation & Video Production — Vowlyn')
@section('og_description', 'Scroll-stopping brand films, social edits, and campaign content designed to earn attention.')
@section('twitter_title', 'Content Creation & Video Production — Vowlyn')
@section('twitter_description', 'Strategy-led video, social campaigns, and brand films by Vowlyn.')

@push('head')
    <style>
        .content-studio {
            --paper: #f7f9ff;
            --accent: #18d2ff;
            --ink: #0d1d2a;
            --stage: #0d0420;
            --blush: #ffd1dc;
            font-family: var(--font-sans);
        }
        .content-studio .editorial {
            font-family: var(--font-display);
            font-weight: 700;
            font-style: normal;
            text-wrap: balance;
        }
        .content-studio .hairline-grid {
            background-image: linear-gradient(rgba(255,255,255,.055) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.055) 1px, transparent 1px);
            background-size: 72px 72px;
        }
        .content-studio .film-frame::after {
            content: ''; position: absolute; inset: 0; pointer-events: none;
            background: linear-gradient(180deg, transparent 48%, rgba(0,0,0,.72) 100%);
        }
        .content-studio .marquee-studio { animation: content-marquee 24s linear infinite; }
        @keyframes content-marquee { to { transform: translateX(-50%); } }
        @media (prefers-reduced-motion: reduce) { .content-studio .marquee-studio { animation: none; } }
    </style>
@endpush

@php
    $services = [
        ['01', 'Creative direction', 'A clear visual premise, reference language, hooks, and a practical shot plan before production begins.'],
        ['02', 'Production', 'Purposeful capture for brand stories, social campaigns, founder content, products, and physical spaces.'],
        ['03', 'Post-production', 'Editorial rhythm, sound, color, graphics, captions, and platform-ready aspect ratios.'],
        ['04', 'Content systems', 'A reusable library of hero edits, cutdowns, hooks, and stills—not a one-post deliverable.'],
    ];
@endphp

@section('content')
    <div class="content-studio bg-[#0d0420] text-white overflow-hidden">
        {{-- HERO --}}
        <section class="relative min-h-screen pt-32 pb-16 lg:pt-40 lg:pb-24 hairline-grid">
            <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
                <div class="absolute -top-24 -right-16 size-[32rem] rounded-full bg-[#18d2ff]/12 blur-3xl"></div>
                <div class="absolute left-[8%] bottom-[5%] size-72 rounded-full bg-[#c8459b]/20 blur-3xl"></div>
            </div>

            <div class="container-vw relative">
                <div class="grid lg:grid-cols-[1.03fr_.97fr] gap-14 lg:gap-8 items-center">
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-8" data-reveal>
                            <span class="h-px w-10 bg-[#18d2ff]"></span>
                            <span class="text-[10px] font-mono uppercase tracking-[.24em] text-white/55">Vowlyn Content Studio · 2026</span>
                        </div>

                        <h1 class="editorial text-[clamp(4.4rem,10vw,9.5rem)] leading-[.78] tracking-[-.065em]" data-reveal data-reveal-delay="0.04">
                            Make them<br>
                            <span class="text-brand-gradient">feel</span> it.
                        </h1>

                        <div class="mt-10 lg:ml-[22%] max-w-xl" data-reveal data-reveal-delay="0.1">
                            <p class="text-lg sm:text-xl text-white/68 leading-relaxed">
                                Strategy-led films and social content with a point of view. We turn one production into a living library of stories built to stop the scroll—and stay remembered.
                            </p>
                            <div class="flex flex-wrap items-center gap-3 mt-8">
                                <a href="#work" class="inline-flex items-center gap-3 rounded-full bg-[#18d2ff] px-6 py-3.5 text-sm font-bold text-[#0d0420] transition hover:scale-[1.03] hover:bg-[#7ee8ff] focus:outline-none focus:ring-2 focus:ring-[#18d2ff] focus:ring-offset-4 focus:ring-offset-[#0d0420]">
                                    Watch the work <i data-lucide="arrow-down-right" class="size-4"></i>
                                </a>
                                <a href="{{ url('/#contact') }}" class="inline-flex items-center gap-3 rounded-full border border-white/20 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-white hover:text-black">
                                    Plan a shoot <i data-lucide="arrow-up-right" class="size-4"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    @if ($featuredFilm)
                        <div class="relative mx-auto w-full max-w-[31rem] lg:mr-4" data-reveal data-reveal-delay="0.12">
                            <div class="absolute -left-10 top-16 z-10 hidden sm:block rounded-full bg-[#e6e6fa] px-4 py-2 text-[10px] font-mono uppercase tracking-[.2em] text-[#0d1d2a] rotate-[-8deg]">Selected work / 01</div>
                            <x-content-video-player :film="$featuredFilm" :hero="true" />
                            <div class="absolute -bottom-5 -right-3 sm:-right-8 grid size-24 place-items-center rounded-full border border-[#18d2ff]/40 bg-[#0d0420] text-center text-[9px] font-mono uppercase tracking-[.16em] text-[#18d2ff] rotate-6">Original<br>Stories</div>
                        </div>
                    @else
                        <div class="rounded-[2rem] border border-white/15 p-10">
                            <p class="editorial text-4xl">New work is on the way.</p>
                            <p class="mt-4 text-white/70">Let’s talk about the story you want to tell.</p>
                        </div>
                    @endif
                </div>

                <div class="mt-16 flex flex-col gap-5 border-t border-white/12 pt-6 sm:flex-row sm:items-center sm:justify-between" data-reveal>
                    <p class="text-[10px] font-mono uppercase tracking-[.2em] text-white/40">Strategy / Production / Edit / Delivery</p>
                    <p class="max-w-md text-sm leading-relaxed text-white/48">One idea, shaped into every format your audience actually watches.</p>
                </div>
            </div>
        </section>

        {{-- MOTION TICKER --}}
        <div class="overflow-hidden border-y border-white/10 py-4 text-white" style="background: var(--gradient-brand);" aria-hidden="true">
            <div class="marquee-studio flex w-max items-center whitespace-nowrap">
                @for ($pass = 0; $pass < 2; $pass++)
                    @foreach (['CREATIVE DIRECTION', 'BRAND FILMS', 'SOCIAL CONTENT', 'CAMPAIGN EDITS', 'MOTION DESIGN'] as $item)
                        <span class="mx-6 text-xs font-bold tracking-[.2em]">{{ $item }}</span>
                        <span class="editorial text-2xl">✳</span>
                    @endforeach
                @endfor
            </div>
        </div>

        {{-- WORK --}}
        <section id="work" class="bg-[var(--paper)] py-24 text-[var(--ink)] lg:py-32">
            <div class="container-vw">
                <div class="grid gap-8 border-b border-black/15 pb-12 lg:grid-cols-[1fr_1.3fr] lg:items-end">
                    <div data-reveal>
                        <p class="text-[10px] font-mono uppercase tracking-[.22em] text-black/45">Selected content projects</p>
                        <h2 class="editorial mt-4 text-6xl leading-[.88] tracking-[-.045em] sm:text-7xl lg:text-8xl">Original content.<br><span class="text-brand-gradient">Lasting impact.</span></h2>
                    </div>
                    <p class="max-w-2xl text-lg leading-relaxed text-black/60 lg:justify-self-end" data-reveal data-reveal-delay="0.08">
                        Brand films, social edits, and campaign stories. Explore the work and discover how each project brings a different place, product, or idea to life.
                    </p>
                </div>

                <div class="mt-14 grid gap-x-5 gap-y-14 sm:grid-cols-2 lg:grid-cols-3" data-stagger>
                    @forelse ($films as $index => $film)
                        <article data-stagger-item class="group min-w-0 {{ $index === 1 ? 'lg:mt-24' : '' }}">
                            <x-content-video-player :film="$film" :number="$films->firstItem() + $index" />
                            <div class="mt-5 flex items-start justify-between gap-5 border-t border-black/15 pt-4">
                                <div class="min-w-0 break-words">
                                    <p class="text-[9px] font-mono uppercase tracking-[.2em] text-black/60">{{ $film->type }}@if ($film->client) · {{ $film->client }}@endif</p>
                                    <h3 class="editorial mt-1 text-3xl tracking-[-.025em]">{{ $film->title }}</h3>
                                    <p class="mt-3 max-w-sm text-sm leading-relaxed text-black/65">{{ $film->note }}</p>
                                    @if ($film->transcript)
                                        <details class="mt-4 text-sm">
                                            <summary class="cursor-pointer font-semibold focus-visible:outline-2">Transcript / description</summary>
                                            <p class="mt-3 whitespace-pre-line leading-relaxed">{{ $film->transcript }}</p>
                                        </details>
                                    @endif
                                </div>
                                <i data-lucide="arrow-up-right" class="mt-1 size-5 shrink-0 text-black/35" aria-hidden="true"></i>
                            </div>
                        </article>
                    @empty
                        <p class="text-black/65">New work is on the way. Check back soon for our latest films.</p>
                    @endforelse
                </div>
                <div class="mt-12">{{ $films->links() }}</div>
            </div>
        </section>

        {{-- SERVICES / PROCESS --}}
        <section class="relative bg-brand-stage grain py-24 lg:py-32">
            <div class="container-vw">
                <div class="grid gap-10 lg:grid-cols-[.72fr_1.28fr]">
                    <div class="lg:sticky lg:top-32 lg:self-start" data-reveal>
                        <p class="text-[10px] font-mono uppercase tracking-[.22em] text-[#18d2ff]">From brief to feed</p>
                        <h2 class="editorial mt-5 text-6xl leading-[.88] tracking-[-.045em] sm:text-7xl">A content engine,<br><span class="text-brand-gradient">not a content dump.</span></h2>
                        <p class="mt-7 max-w-md text-base leading-relaxed text-white/55">Every frame should have a job. We build the core idea first, then create a flexible set of assets around it.</p>
                    </div>

                    <div class="border-t border-white/15" data-stagger>
                        @foreach ($services as [$number, $title, $description])
                            <article data-stagger-item class="group grid gap-5 border-b border-white/15 py-8 sm:grid-cols-[5rem_1fr_auto] sm:items-start lg:py-10">
                                <span class="text-[10px] font-mono tracking-[.2em] text-[#18d2ff]">/{{ $number }}</span>
                                <div>
                                    <h3 class="editorial text-3xl tracking-[-.025em] sm:text-4xl">{{ $title }}</h3>
                                    <p class="mt-3 max-w-xl text-sm leading-relaxed text-white/50 sm:text-base">{{ $description }}</p>
                                </div>
                                <span class="hidden size-11 place-items-center rounded-full border border-white/15 text-white/45 transition group-hover:rotate-45 group-hover:border-[#18d2ff] group-hover:text-[#18d2ff] sm:grid">
                                    <i data-lucide="plus" class="size-4"></i>
                                </span>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <section class="relative overflow-hidden bg-gradient-to-br from-[#ffe7ee] via-[#ffd1dc] to-[#e6e6fa] py-24 text-[#0d1d2a] lg:py-32">
            <div class="absolute right-[-6rem] top-[-7rem] size-[24rem] rounded-full border-[50px] border-[#7b41b3]/8" aria-hidden="true"></div>
            <div class="container-vw relative text-center">
                <p class="text-[10px] font-mono uppercase tracking-[.24em] text-black/55" data-reveal>Have a story worth making?</p>
                <h2 class="editorial mx-auto mt-5 max-w-5xl text-[clamp(4rem,9vw,8.5rem)] leading-[.82] tracking-[-.06em]" data-reveal data-reveal-delay="0.05">
                    Let’s make the next thing people <span class="text-brand-gradient">remember.</span>
                </h2>
                <a href="{{ url('/#contact') }}" data-magnetic="0.16"
                   class="mt-10 inline-flex items-center gap-3 rounded-full bg-[#0d0420] px-7 py-4 text-sm font-bold text-white transition hover:bg-[#4b0082] hover:shadow-[0_14px_40px_rgba(75,0,130,.25)]" data-reveal data-reveal-delay="0.1">
                    Start a content project <i data-lucide="arrow-up-right" class="size-4"></i>
                </a>
            </div>
        </section>
    </div>
@endsection
