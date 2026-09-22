{{-- PERFORMANCE MARKETING — measurement-first growth practice --}}
@extends('layouts.app')

@section('title', 'Marketing & Paid Media — Vowlyn')
@section('description', 'Measurement-led paid search, paid social, landing pages, creative testing, and conversion optimization for growing brands.')
@section('canonical', 'https://vowlyn.com/performance-marketing')
@section('og_title', 'Marketing & Paid Media — Vowlyn')
@section('og_description', 'Campaign strategy, creative testing, conversion tracking, and landing-page optimization connected to business outcomes.')
@section('twitter_title', 'Marketing & Paid Media — Vowlyn')
@section('twitter_description', 'Measurement-led acquisition and conversion systems for growing brands.')

@push('head')
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => 'Marketing',
        'provider' => ['@type' => 'Organization', 'name' => 'Vowlyn', 'url' => 'https://vowlyn.com'],
        'url' => 'https://vowlyn.com/performance-marketing',
        'description' => 'Measurement-led paid search, paid social, landing pages, creative testing, and conversion optimization.',
        'areaServed' => ['North America', 'Europe', 'MENA'],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    <style>
        .campaign-poster {
            position: relative;
            isolation: isolate;
            aspect-ratio: 1 / 1.04;
            overflow: hidden;
            border: 1px solid rgb(255 255 255 / 75%);
            border-radius: 2rem;
            background: #10051f;
            box-shadow: 0 42px 110px -42px rgb(75 0 130 / 58%);
        }

        .campaign-poster::before {
            position: absolute;
            inset: 0;
            z-index: -2;
            content: '';
            background-image:
                linear-gradient(rgb(255 255 255 / 7%) 1px, transparent 1px),
                linear-gradient(90deg, rgb(255 255 255 / 7%) 1px, transparent 1px);
            background-size: 12.5% 12.5%;
            mask-image: linear-gradient(to bottom, #000 10%, transparent 88%);
        }

        .campaign-poster::after {
            position: absolute;
            inset: auto -18% -36% 16%;
            z-index: -1;
            aspect-ratio: 1;
            content: '';
            border-radius: 999px;
            background: radial-gradient(circle, rgb(239 72 154 / 72%) 0%, rgb(124 58 237 / 38%) 38%, transparent 70%);
            filter: blur(24px);
        }

        .campaign-poster__orb {
            position: absolute;
            top: 15%;
            right: 7%;
            width: 35%;
            aspect-ratio: 1;
            border-radius: 999px;
            background:
                radial-gradient(circle at 36% 30%, rgb(255 255 255 / 92%) 0 3%, transparent 4%),
                radial-gradient(circle at 40% 35%, #ff8bc1 0%, #bd63d7 35%, #6247c8 68%, #18c6f4 100%);
            box-shadow: inset -18px -22px 38px rgb(14 5 31 / 28%), 0 24px 58px rgb(18 198 244 / 22%);
            transition: transform 550ms cubic-bezier(.2,.8,.2,1);
        }

        .campaign-poster:hover .campaign-poster__orb {
            transform: translate3d(-8px, 8px, 0) rotate(-8deg);
        }

        .campaign-poster__outline {
            color: transparent;
            -webkit-text-stroke: 1px rgb(255 255 255 / 62%);
        }

        .campaign-poster__ticker {
            transform: rotate(-6deg) translate3d(-4%, 0, 0);
            width: 110%;
        }

        @media (max-width: 639px) {
            .campaign-poster {
                border-radius: 1.5rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .campaign-poster__orb {
                transition: none;
            }
        }
    </style>
@endpush

@php
    $capabilities = [
        ['search', 'Paid search', 'Intent-led campaign structure, query control, offer alignment, and budget discipline.'],
        ['users-round', 'Paid social', 'Audience strategy and creative testing built around a clear conversion journey.'],
        ['layout-panel-top', 'Landing pages', 'Focused pages that match the promise of the ad and remove friction before conversion.'],
        ['pencil-ruler', 'Creative testing', 'A repeatable system for testing hooks, formats, messages, and offers—not random asset churn.'],
        ['scan-eye', 'Measurement', 'Conversion tracking, attribution hygiene, and reporting tied to the actions that matter.'],
        ['trending-up', 'Optimization', 'Regular decisions across spend, targeting, creative, and conversion rate based on useful evidence.'],
    ];
    $phases = [
        ['01', 'Instrument', 'Define the valuable action, audit tracking, and create a dependable measurement baseline.'],
        ['02', 'Launch', 'Build the channel plan, campaign structure, creative variants, and conversion-focused landing path.'],
        ['03', 'Learn', 'Read signal quality, search terms, audience behavior, creative response, and on-page friction.'],
        ['04', 'Scale', 'Move budget toward repeatable performance while protecting efficiency and lead quality.'],
    ];
@endphp

@section('content')
    <main class="overflow-hidden bg-mesh-light text-on-surface">
        <section class="relative min-h-[92vh] pt-32 pb-20 lg:pt-44 lg:pb-28">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0">
                <div class="absolute -left-24 top-24 size-[30rem] rounded-full bg-primary-400/20 blur-3xl"></div>
                <div class="absolute -right-28 top-0 size-[32rem] rounded-full bg-blush-300/35 blur-3xl"></div>
            </div>
            <div class="container-vw relative grid items-center gap-14 lg:grid-cols-[1.08fr_.92fr]">
                <div>
                    <div class="mb-7 flex items-center gap-3" data-reveal>
                        <span class="eyebrow !text-primary-700">Marketing</span>
                    </div>
                    <h1 class="headline-display max-w-4xl text-5xl leading-[.94] sm:text-6xl lg:text-7xl xl:text-[5.5rem]" data-reveal data-reveal-delay="0.04">
                        Turn attention into <span class="text-brand-gradient">measurable growth.</span>
                    </h1>
                    <p class="mt-7 max-w-2xl text-lg leading-relaxed text-on-surface-muted sm:text-xl" data-reveal data-reveal-delay="0.08">
                        Paid media works better when the ad, landing page, tracking, and follow-up operate as one system. Vowlyn plans and improves that entire path—from first signal to qualified action.
                    </p>
                    <div class="mt-9 flex flex-wrap gap-3" data-reveal data-reveal-delay="0.12">
                        <a href="{{ url('/#contact') }}" class="btn btn-primary">Plan a growth campaign <i data-lucide="arrow-up-right" class="size-4"></i></a>
                        <a href="#capabilities" class="btn btn-outline">Explore capabilities <i data-lucide="arrow-down" class="size-4"></i></a>
                    </div>
                </div>

                <div class="relative mx-auto w-full max-w-xl" data-reveal data-reveal-delay="0.12">
                    <div class="campaign-poster text-white" role="img" aria-label="Editorial illustration showing attention becoming intent and action across connected marketing channels.">
                        <div aria-hidden="true" class="absolute inset-0">
                            <div class="campaign-poster__orb">
                                <span class="absolute inset-0 grid place-items-center font-mono text-[10px] font-semibold uppercase tracking-[.24em] text-white/85">Focus</span>
                            </div>

                            <div class="absolute inset-x-[7%] top-[7%] flex items-center justify-between border-b border-white/20 pb-3 font-mono text-[8px] uppercase tracking-[.22em] text-white/55 sm:text-[9px]">
                                <span>Vowlyn marketing</span>
                                <span>Field note 01</span>
                            </div>

                            <div class="absolute left-[7%] top-[23%] font-display text-[clamp(3.6rem,10vw,7.4rem)] font-black uppercase leading-[.72] tracking-[-.07em]">
                                <span class="block">Make</span>
                                <span class="campaign-poster__outline ml-[12%] block">Every</span>
                                <span class="block bg-gradient-to-r from-[#22c7f5] via-[#8a62e8] to-[#ff66a8] bg-clip-text text-transparent">Move</span>
                            </div>

                            <div class="campaign-poster__ticker absolute left-0 top-[67%] overflow-hidden border-y border-[#180828] bg-[#f5eeff] py-2.5 text-[#180828] shadow-2xl sm:py-3">
                                <div class="flex whitespace-nowrap font-mono text-[8px] font-semibold uppercase tracking-[.22em] sm:text-[10px]">
                                    <span class="px-4">Search</span><span aria-hidden="true">●</span>
                                    <span class="px-4">Social</span><span aria-hidden="true">●</span>
                                    <span class="px-4">Creative</span><span aria-hidden="true">●</span>
                                    <span class="px-4">Landing pages</span><span aria-hidden="true">●</span>
                                    <span class="px-4">Measurement</span>
                                </div>
                            </div>

                            <div class="absolute inset-x-[7%] bottom-[7%] grid grid-cols-3 border-y border-white/20 py-3 sm:py-4">
                                @foreach ([['01', 'Attention'], ['02', 'Intent'], ['03', 'Action']] as [$number, $label])
                                    <div class="{{ !$loop->first ? 'border-l border-white/20 pl-3 sm:pl-5' : '' }}">
                                        <span class="block font-mono text-[8px] tracking-[.2em] text-primary-300">{{ $number }}</span>
                                        <span class="mt-1 block font-display text-xs font-semibold sm:text-base">{{ $label }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="capabilities" class="section-vw bg-white">
            <div class="container-vw">
                <div class="grid gap-7 lg:grid-cols-[.8fr_1.2fr] lg:items-end">
                    <div><span class="eyebrow !text-primary-700" data-reveal>Connected capabilities</span><h2 class="headline-display mt-4 text-4xl sm:text-5xl lg:text-6xl" data-reveal>Every part of the <span class="text-brand-gradient">conversion path.</span></h2></div>
                    <p class="max-w-2xl text-lg leading-relaxed text-on-surface-muted lg:justify-self-end" data-reveal>We combine media buying with the product and creative skills needed after the click. Scope is shaped around the channels and constraints of each business.</p>
                </div>
                <div class="mt-14 grid gap-px overflow-hidden rounded-3xl border border-on-surface/10 bg-on-surface/10 sm:grid-cols-2 lg:grid-cols-3" data-stagger>
                    @foreach ($capabilities as [$icon, $title, $copy])
                        <article data-stagger-item class="bg-[#f8f9ff] p-7 lg:p-9">
                            <span class="grid size-11 place-items-center rounded-full bg-primary-100 text-primary-700"><i data-lucide="{{ $icon }}" class="size-5"></i></span>
                            <h3 class="mt-8 font-display text-2xl font-bold">{{ $title }}</h3>
                            <p class="mt-3 text-sm leading-relaxed text-on-surface-muted">{{ $copy }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section-vw relative bg-brand-stage text-white grain">
            <div class="container-vw grid gap-12 lg:grid-cols-[.72fr_1.28fr]">
                <div class="lg:sticky lg:top-32 lg:self-start">
                    <span class="eyebrow !text-primary-300" data-reveal>How we operate</span>
                    <h2 class="headline-display mt-5 text-5xl lg:text-6xl" data-reveal>Evidence before <span class="text-brand-gradient">assumption.</span></h2>
                    <p class="mt-6 max-w-md leading-relaxed text-white/60">The goal is a learning system the team can understand: clean inputs, explicit hypotheses, and decisions connected to commercial outcomes.</p>
                </div>
                <div class="border-t border-white/15" data-stagger>
                    @foreach ($phases as [$number, $title, $copy])
                        <article data-stagger-item class="grid gap-4 border-b border-white/15 py-8 sm:grid-cols-[4rem_1fr] lg:py-10">
                            <span class="text-[10px] font-mono tracking-[.2em] text-primary-300">/{{ $number }}</span>
                            <div><h3 class="font-display text-3xl font-bold sm:text-4xl">{{ $title }}</h3><p class="mt-3 max-w-2xl leading-relaxed text-white/55">{{ $copy }}</p></div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section-vw bg-gradient-to-br from-[#ffe7ee] via-[#f6efff] to-[#eaf8ff] text-center">
            <div class="container-vw">
                <p class="eyebrow !text-primary-700" data-reveal>Start with the real constraint</p>
                <h2 class="headline-display mx-auto mt-5 max-w-5xl text-5xl sm:text-6xl lg:text-7xl" data-reveal>Bring us the funnel. We’ll find the <span class="text-brand-gradient">next useful move.</span></h2>
                <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-on-surface-muted">Share your channels, offer, audience, and current measurement setup. We’ll recommend a focused first engagement—without promising results before seeing the evidence.</p>
                <a href="{{ url('/#contact') }}" class="btn btn-primary mt-9">Discuss marketing <i data-lucide="arrow-up-right" class="size-4"></i></a>
            </div>
        </section>
    </main>
@endsection
