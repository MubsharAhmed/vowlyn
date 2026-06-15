{{-- ============================================================================
SERVICES — "Cinema Strip"
Distinctive concept: a pinned horizontal scroll where each service panel rolls
past full-viewport like a 70mm film strip. Page scroll drives X-translate of
the strip via GSAP ScrollTrigger. Each panel has its own color story.
============================================================================ --}}
@extends('layouts.app')

@section('title', 'Services — Vowlyn')
@section('description', 'Six disciplines, one studio. Every service crafted edge-to-edge for the products you build.')

@php
    $cinema = [
        [
            'no' => '01', 'name' => 'Modern Web',
            'tag' => 'Interface · Performance',
            'pitch' => 'Edge-rendered apps with motion-grade interaction design.',
            'pillars' => ['Design systems', 'SSR / Edge', 'Animation', 'A11y'],
            'stops' => ['#18d2ff', '#5c7cf5'],
        ],
        [
            'no' => '02', 'name' => 'Mobile Apps',
            'tag' => 'iOS · Android · Cross-platform',
            'pitch' => 'Native-feel apps that ship to both stores from one codebase.',
            'pillars' => ['React Native', 'Flutter', 'Offline-first', 'OTA updates'],
            'stops' => ['#5c7cf5', '#7b41b3'],
        ],
        [
            'no' => '03', 'name' => 'AI Engineering',
            'tag' => 'LLM Orchestration · Agents',
            'pitch' => 'Agentic workflows wired into your existing product surface.',
            'pillars' => ['RAG pipelines', 'Eval harnesses', 'Fine-tuning', 'Guardrails'],
            'stops' => ['#7b41b3', '#c8459b'],
        ],
        [
            'no' => '04', 'name' => 'SaaS Platforms',
            'tag' => 'Multi-tenant · Billing',
            'pitch' => 'Production-ready platforms with metering, plans, and observability.',
            'pillars' => ['Multi-tenant', 'Stripe billing', 'RBAC', 'Audit trails'],
            'stops' => ['#c8459b', '#ff7ab8'],
        ],
        [
            'no' => '05', 'name' => 'Enterprise Security',
            'tag' => 'SOC 2 · Threat Modelling',
            'pitch' => 'Hardening, secrets hygiene, and compliance done quietly.',
            'pillars' => ['SSO / SAML', 'Pen testing', 'Compliance', 'Zero-trust'],
            'stops' => ['#ff7ab8', '#ef4444'],
        ],
        [
            'no' => '06', 'name' => 'Cloud DevOps',
            'tag' => 'AWS · GCP · Cloudflare',
            'pitch' => 'Infrastructure that ships itself — repeatable, observable, calm.',
            'pillars' => ['IaC / Terraform', 'CI/CD', 'Observability', 'Cost ops'],
            'stops' => ['#18d2ff', '#c8459b'],
        ],
    ];
@endphp

@section('content')
    {{-- ═══ PAGE HERO ═══ --}}
    <section class="relative bg-brand-stage text-white overflow-hidden grain pt-28 lg:pt-36 pb-16 lg:pb-24">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute top-10 -left-32 size-[28rem] rounded-full bg-primary-700/30 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 size-[24rem] rounded-full bg-blush-500/25 blur-3xl"></div>
        </div>

        <div class="container-vw relative">
            <div class="flex items-center gap-3 mb-6" data-reveal>
                <span class="h-px w-10 bg-white/30"></span>
                <span class="eyebrow !text-primary-300">Services Index</span>
                <span class="text-[10px] font-mono uppercase tracking-[0.22em] text-white/35">06 disciplines</span>
            </div>

            <div class="grid lg:grid-cols-[1.3fr_1fr] gap-10 lg:gap-16 items-end">
                <h1 class="headline-display text-5xl sm:text-6xl lg:text-7xl xl:text-[5.5rem] text-white leading-[0.95]" data-reveal data-reveal-delay="0.05">
                    Six disciplines.<br/>
                    <span class="text-brand-gradient">One studio,</span> end-to-end.
                </h1>
                <div class="space-y-4" data-reveal data-reveal-delay="0.12">
                    <p class="text-white/65 text-lg leading-relaxed">
                        Scroll the strip below to roll through each service. Every panel is a self-contained craft — built by the same team that integrates them.
                    </p>
                    <a href="#cinema" class="inline-flex items-center gap-2 text-sm font-mono uppercase tracking-[0.18em] text-white/70 hover:text-white transition-colors">
                        <i data-lucide="arrow-down" class="size-4"></i>
                        Play the reel
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ CINEMA STRIP — horizontal scroll-jacked, pinned ═══ --}}
    <section id="cinema" class="relative bg-[#06010f] text-white overflow-hidden" data-cinema-section>
        {{-- Frame counter / scrub HUD (sticky at top of pinned area) --}}
        <div class="absolute top-6 left-6 lg:top-8 lg:left-10 z-20 pointer-events-none flex items-center gap-3">
            <span class="size-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-[10px] font-mono uppercase tracking-[0.2em] text-white/55">Reel · 06 panels</span>
        </div>
        <div class="absolute top-6 right-6 lg:top-8 lg:right-10 z-20 pointer-events-none">
            <span class="text-[10px] font-mono uppercase tracking-[0.2em] text-white/55">
                <span data-cinema-counter>01</span> / 06
            </span>
        </div>

        {{-- Progress bar pinned to top edge --}}
        <div class="absolute top-0 inset-x-0 h-[2px] bg-white/10 z-20">
            <div class="h-full origin-left scale-x-0 bg-gradient-to-r from-primary-400 via-blush-400 to-primary-700" data-cinema-progress></div>
        </div>

        {{-- Pinned viewport — 100vh tall; track translates horizontally on scroll --}}
        <div class="cinema-pin h-[100vh] flex items-center" data-cinema-pin>
            <div class="cinema-track flex h-[80vh] gap-5 lg:gap-7 px-6 lg:px-10 will-change-transform" data-cinema-track>
                @foreach ($cinema as $i => $p)
                    <article class="cinema-panel relative shrink-0 w-[88vw] sm:w-[78vw] lg:w-[68vw] xl:w-[56vw] h-full rounded-3xl overflow-hidden border border-white/[0.08] flex flex-col justify-between p-8 lg:p-12"
                             style="background: radial-gradient(at top left, {{ $p['stops'][0] }}22 0%, transparent 55%), radial-gradient(at bottom right, {{ $p['stops'][1] }}28 0%, transparent 60%), #0a041a;">

                        {{-- Etched grid texture --}}
                        <div aria-hidden="true" class="absolute inset-0 plate-grid opacity-[0.08]"></div>

                        {{-- Corner registration marks --}}
                        <span aria-hidden="true" class="absolute top-5 left-5 size-3 border-t border-l border-white/30"></span>
                        <span aria-hidden="true" class="absolute top-5 right-5 size-3 border-t border-r border-white/30"></span>
                        <span aria-hidden="true" class="absolute bottom-5 left-5 size-3 border-b border-l border-white/30"></span>
                        <span aria-hidden="true" class="absolute bottom-5 right-5 size-3 border-b border-r border-white/30"></span>

                        {{-- Top row --}}
                        <header class="relative flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] font-mono uppercase tracking-[0.22em] text-white/45">SVC.{{ $p['no'] }}</span>
                                <span class="h-px w-6 bg-white/15"></span>
                                <span class="text-[10px] font-mono uppercase tracking-[0.18em] text-white/55">{{ $p['tag'] }}</span>
                            </div>
                            <span class="hidden sm:inline-flex items-center gap-1 text-[10px] font-mono uppercase tracking-[0.18em] px-2 py-0.5 rounded-full border border-white/12 text-white/55">
                                <span class="size-1.5 rounded-full" style="background: {{ $p['stops'][1] }};"></span>
                                Active
                            </span>
                        </header>

                        {{-- Middle: massive name --}}
                        <div class="relative flex-1 flex items-center">
                            <div class="w-full">
                                <h2 class="font-display font-bold leading-[0.9] tracking-tight text-[clamp(3.5rem,9vw,8rem)]"
                                    style="background: linear-gradient(135deg, #ffffff 0%, #ffffff 50%, {{ $p['stops'][1] }} 100%); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">
                                    {{ $p['name'] }}
                                </h2>
                                <p class="mt-6 max-w-xl text-lg lg:text-xl text-white/75 leading-relaxed">{{ $p['pitch'] }}</p>
                            </div>
                        </div>

                        {{-- Bottom: pillars chips + CTA --}}
                        <footer class="relative flex flex-wrap items-end justify-between gap-6">
                            <div class="flex flex-wrap gap-2">
                                @foreach ($p['pillars'] as $pillar)
                                    <span class="text-[11px] font-mono uppercase tracking-[0.14em] px-2.5 py-1 rounded-full border border-white/12 text-white/75 bg-white/[0.03]">
                                        {{ $pillar }}
                                    </span>
                                @endforeach
                            </div>
                            <a href="#" class="group inline-flex items-center gap-2 text-sm font-mono uppercase tracking-[0.18em] text-white">
                                Engage
                                <span class="grid place-items-center size-9 rounded-full border border-white/20 group-hover:bg-white group-hover:text-[#06010f] transition-colors">
                                    <i data-lucide="arrow-up-right" class="size-4"></i>
                                </span>
                            </a>
                        </footer>
                    </article>
                @endforeach

                {{-- Trailing end-card so users get to "the end" before unpin --}}
                <div class="shrink-0 w-[40vw] h-full flex items-center justify-center text-center px-8">
                    <div>
                        <p class="text-[10px] font-mono uppercase tracking-[0.2em] text-white/35 mb-3">END OF REEL</p>
                        <p class="font-display text-3xl lg:text-4xl text-white/80 leading-tight">Need all six,<br/>or a custom mix?</p>
                        <a href="{{ url('/#contact') }}" class="btn btn-primary mt-7 !text-sm">
                            Brief us <i data-lucide="arrow-up-right" class="size-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ SERVICE LEDGER — fallback / SEO-friendly index ═══ --}}
    <section class="relative bg-mesh-light py-24 lg:py-32">
        <div class="container-vw">
            <div class="flex items-end justify-between mb-12 lg:mb-16 flex-wrap gap-4">
                <div>
                    <span class="eyebrow !text-primary-700">The Ledger</span>
                    <h2 class="headline-display text-4xl lg:text-5xl mt-3">Engagements at a glance.</h2>
                </div>
                <p class="text-on-surface-muted max-w-md">Each engagement starts with a paid discovery sprint, scoped in 7 days, with a one-page brief at the end.</p>
            </div>

            <ul class="divide-y divide-on-surface/10 border-y border-on-surface/10">
                @foreach ($cinema as $p)
                    <li class="group">
                        <a href="#" class="flex flex-wrap items-baseline justify-between gap-4 py-6 lg:py-7 transition-colors hover:bg-white/40 -mx-4 px-4 rounded-2xl">
                            <span class="text-[10px] font-mono uppercase tracking-[0.22em] text-on-surface-muted w-12 shrink-0">{{ $p['no'] }}</span>
                            <h3 class="font-display text-2xl lg:text-3xl flex-1 min-w-[200px]">{{ $p['name'] }}</h3>
                            <span class="text-sm font-mono uppercase tracking-[0.16em] text-on-surface-muted hidden md:block flex-1">{{ $p['tag'] }}</span>
                            <span class="inline-flex items-center gap-2 text-sm font-medium">
                                Brief
                                <i data-lucide="arrow-up-right" class="size-4 transition-transform group-hover:rotate-45"></i>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection
