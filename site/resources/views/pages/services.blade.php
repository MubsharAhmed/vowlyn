{{-- ============================================================================
SERVICES — "Cinema Strip"
Distinctive concept: a pinned horizontal scroll where each service panel rolls
past full-viewport like a 70mm film strip. Page scroll drives X-translate of
the strip via GSAP ScrollTrigger. Each panel has its own color story.
============================================================================ --}}
@extends('layouts.app')

@section('title', 'Software Development Services — Web, Mobile, AI & SaaS | Vowlyn')
@section('description', 'Software development services from Vowlyn: web apps, mobile apps, AI engineering, SaaS platforms, enterprise security, and cloud DevOps. One senior studio, end-to-end — you own the code.')
@section('canonical', 'https://vowlyn.com/services')
@section('og_title', 'Software Development Services | Vowlyn')
@section('og_description', 'Web, mobile, AI, SaaS, security, and cloud DevOps — delivered by one senior studio, end-to-end.')
@section('twitter_title', 'Software Development Services | Vowlyn')
@section('twitter_description', 'Web, mobile, AI, SaaS, security, and cloud DevOps from one senior studio.')

@push('head')
    {{-- Service + OfferCatalog — AI-citable definition of the six disciplines --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": "Service",
      "serviceType": "Custom software development",
      "name": "Software Development Services",
      "provider": { "@id": "https://vowlyn.com/#organization" },
      "areaServed": ["North America", "Europe", "MENA"],
      "url": "https://vowlyn.com/services",
      "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "Software development services",
        "itemListElement": [
          { "@type": "Offer", "itemOffered": { "@type": "Service", "name": "Modern Web App Development", "description": "Edge-rendered, SEO-clean web platforms built with Next.js, headless CMS, and design systems." } },
          { "@type": "Offer", "itemOffered": { "@type": "Service", "name": "Mobile App Development", "description": "React Native and native iOS/Android apps shipped to both stores from one codebase." } },
          { "@type": "Offer", "itemOffered": { "@type": "Service", "name": "AI Engineering & Integration", "description": "RAG assistants, LLM pipelines, and computer vision wired into your product." } },
          { "@type": "Offer", "itemOffered": { "@type": "Service", "name": "SaaS Platform Development", "description": "Multi-tenant SaaS with subscription billing, RBAC, and observability from day one." } },
          { "@type": "Offer", "itemOffered": { "@type": "Service", "name": "Enterprise Security", "description": "SOC 2-aligned hardening, SSO/SAML, secrets hygiene, compliance workflows." } },
          { "@type": "Offer", "itemOffered": { "@type": "Service", "name": "Cloud DevOps", "description": "AWS/GCP IaC, CI/CD pipelines, 24/7 observability — infrastructure that ships itself." } }
        ]
      }
    }
    </script>
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        { "@type": "Question", "name": "How much does software development cost with Vowlyn?", "acceptedAnswer": { "@type": "Answer", "text": "Most engagements start with a fixed-fee discovery sprint. Embedded pods run at a flat monthly rate and full builds are milestone-based. All pricing is transparent before any contract is signed." } },
        { "@type": "Question", "name": "How long does it take to build a web or mobile app?", "acceptedAnswer": { "@type": "Answer", "text": "Discovery sprints kick off within a week and run about 7 days. Embedded pods spin up in 2-3 weeks once scope is signed; full builds are milestone-based from there." } },
        { "@type": "Question", "name": "Who owns the code and cloud accounts?", "acceptedAnswer": { "@type": "Answer", "text": "You do. Everything ships to your own repositories and cloud accounts from day one, with no lock-in." } },
        { "@type": "Question", "name": "What is an embedded engineering pod?", "acceptedAnswer": { "@type": "Answer", "text": "A dedicated senior pod (design, engineering, and PM) embedded in your team and shipping every week at a flat monthly rate, with no timesheets." } },
        { "@type": "Question", "name": "What is included after launch?", "acceptedAnswer": { "@type": "Answer", "text": "Every full build includes 90 days of post-launch care. Many clients then move to an embedded pod for ongoing growth." } }
      ]
    }
    </script>
@endpush

@php
    $cinema = [
        [
            'no' => '01', 'slug' => 'web-app-development', 'name' => 'Modern Web App Development',
            'tag' => 'Next.js · Headless CMS · Design Systems',
            'pitch' => 'Edge-rendered, SEO-clean web platforms built with Next.js, headless CMS, and design systems.',
            'pillars' => ['Design systems', 'SSR / Edge', 'Animation', 'A11y'],
            'stops' => ['#18d2ff', '#5c7cf5'],
        ],
        [
            'no' => '02', 'slug' => 'mobile-app-development', 'name' => 'Mobile App Development',
            'tag' => 'React Native · iOS · Android',
            'pitch' => 'React Native and native iOS/Android apps shipped to both stores from one codebase.',
            'pillars' => ['React Native', 'Flutter', 'Offline-first', 'OTA updates'],
            'stops' => ['#5c7cf5', '#7b41b3'],
        ],
        [
            'no' => '03', 'slug' => 'ai-development', 'name' => 'AI Engineering & Integration',
            'tag' => 'RAG · LLM Pipelines · Vision',
            'pitch' => 'RAG assistants, LLM pipelines, and computer vision wired into your product.',
            'pillars' => ['RAG pipelines', 'Eval harnesses', 'Fine-tuning', 'Guardrails'],
            'stops' => ['#7b41b3', '#c8459b'],
        ],
        [
            'no' => '04', 'slug' => 'saas-development', 'name' => 'SaaS Platform Development',
            'tag' => 'Multi-tenant · Billing · RBAC',
            'pitch' => 'Multi-tenant SaaS with subscription billing, RBAC, and observability from day one.',
            'pillars' => ['Multi-tenant', 'Stripe billing', 'RBAC', 'Audit trails'],
            'stops' => ['#c8459b', '#ff7ab8'],
        ],
        [
            'no' => '05', 'slug' => 'enterprise-security', 'name' => 'Enterprise Security',
            'tag' => 'SOC 2 · SSO/SAML · Compliance',
            'pitch' => 'SOC 2-aligned hardening, SSO/SAML, secrets hygiene, compliance workflows.',
            'pillars' => ['SSO / SAML', 'Pen testing', 'Compliance', 'Zero-trust'],
            'stops' => ['#ff7ab8', '#ef4444'],
        ],
        [
            'no' => '06', 'slug' => 'cloud-devops', 'name' => 'Cloud DevOps',
            'tag' => 'AWS · GCP · IaC · CI/CD',
            'pitch' => 'AWS/GCP IaC, CI/CD pipelines, 24/7 observability — infrastructure that ships itself.',
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
                <div>
                    <h1 class="headline-display text-5xl sm:text-6xl lg:text-7xl xl:text-[5.5rem] text-white leading-[0.95]" data-reveal data-reveal-delay="0.05">
                        Software<br/>
                        <span class="text-brand-gradient">Development Services.</span>
                    </h1>
                    <p class="mt-6 font-display text-xl lg:text-2xl text-white/85 leading-snug" data-reveal data-reveal-delay="0.1">
                        Six disciplines. One studio, end-to-end.
                    </p>
                </div>
                <div class="space-y-4" data-reveal data-reveal-delay="0.12">
                    <p class="text-white/65 text-lg leading-relaxed">
                        Vowlyn provides software development services for founders and teams who want one accountable
                        partner — from modern web apps and mobile engineering to AI integration, scalable SaaS,
                        enterprise security, and cloud DevOps.
                    </p>
                    <a href="#cinema" class="inline-flex items-center gap-2 text-sm font-mono uppercase tracking-[0.18em] text-white/70 hover:text-white transition-colors">
                        <i data-lucide="arrow-down" class="size-4"></i>
                        Play the reel
                    </a>
                </div>
            </div>

            {{-- Quick Answer (GEO) --}}
            <x-quick-answer :dark="true" class="mt-10 max-w-2xl" data-reveal data-reveal-delay="0.16">
                Vowlyn offers six software development services — modern web app development, mobile app
                development, AI engineering, SaaS platform development, enterprise security, and cloud DevOps —
                delivered end-to-end by one senior studio. Engagements start with a fixed-fee discovery sprint,
                and you own all code and cloud accounts from day one.
            </x-quick-answer>
        </div>
    </section>

    {{-- ═══ CINEMA STRIP — horizontal scroll-jacked, pinned ═══ --}}
    <section id="cinema" class="relative bg-brand-stage text-white overflow-hidden" data-cinema-section>
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
                            <a href="{{ route('services.show', $p['slug']) }}" class="group inline-flex items-center gap-2 text-sm font-mono uppercase tracking-[0.18em] text-white">
                                Explore
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
                        <a href="{{ route('services.show', $p['slug']) }}" class="flex flex-wrap items-baseline justify-between gap-4 py-6 lg:py-7 transition-colors hover:bg-white/40 -mx-4 px-4 rounded-2xl">
                            <span class="text-[10px] font-mono uppercase tracking-[0.22em] text-on-surface-muted w-12 shrink-0">{{ $p['no'] }}</span>
                            <h3 class="font-display text-2xl lg:text-3xl flex-1 min-w-[200px]">{{ $p['name'] }}</h3>
                            <span class="text-sm font-mono uppercase tracking-[0.16em] text-on-surface-muted hidden md:block flex-1">{{ $p['tag'] }}</span>
                            <span class="inline-flex items-center gap-2 text-sm font-medium">
                                Explore
                                <i data-lucide="arrow-up-right" class="size-4 transition-transform group-hover:rotate-45"></i>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- ═══ CAPABILITY MATRIX — Alpine-filtered control surface ═══ --}}
    @php
        $capabilities = [
            ['cat' => 'Web',    'name' => 'Next.js Platforms',  'icon' => 'monitor',      'note' => 'Edge-rendered, SEO-clean web apps.'],
            ['cat' => 'Web',    'name' => 'Headless CMS',       'icon' => 'layout-panel-top','note' => 'Content systems editors actually enjoy.'],
            ['cat' => 'Web',    'name' => 'Commerce',           'icon' => 'shopping-bag',  'note' => 'Checkout flows that convert.'],
            ['cat' => 'Web',    'name' => 'Design Systems',     'icon' => 'component',     'note' => 'One source of truth, infinite screens.'],
            ['cat' => 'Mobile', 'name' => 'React Native',       'icon' => 'smartphone',    'note' => 'One codebase, both stores.'],
            ['cat' => 'Mobile', 'name' => 'Native iOS / Android','icon' => 'tablet-smartphone','note' => 'Platform-true where it matters.'],
            ['cat' => 'Mobile', 'name' => 'Offline-first',      'icon' => 'wifi-off',      'note' => 'Sync engines that survive the subway.'],
            ['cat' => 'AI',     'name' => 'RAG & Assistants',   'icon' => 'message-square','note' => 'Chat grounded in your own data.'],
            ['cat' => 'AI',     'name' => 'LLM Pipelines',      'icon' => 'workflow',      'note' => 'Evals, guardrails, observability.'],
            ['cat' => 'AI',     'name' => 'Computer Vision',    'icon' => 'scan-eye',      'note' => 'Detection, OCR, quality control.'],
            ['cat' => 'Cloud',  'name' => 'AWS / GCP',          'icon' => 'cloud',         'note' => 'Right-sized infra, no surprises.'],
            ['cat' => 'Cloud',  'name' => 'CI / CD',            'icon' => 'git-branch',    'note' => 'Ship on every merge, safely.'],
            ['cat' => 'Cloud',  'name' => 'Observability',      'icon' => 'activity',      'note' => 'Know it broke before users do.'],
        ];
        $capCats = ['All', 'Web', 'Mobile', 'AI', 'Cloud'];
    @endphp
    <section class="relative bg-brand-stage text-white overflow-hidden grain section-vw"
             x-data="{ cat: 'All' }">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-24 right-1/4 size-[30rem] rounded-full bg-primary-700/25 blur-[120px]"></div>
            <div class="absolute bottom-0 -left-24 size-[24rem] rounded-full bg-blush-500/15 blur-[120px]"></div>
        </div>

        <div class="container-vw relative">
            {{-- Header --}}
            <div class="grid lg:grid-cols-[1fr_auto] gap-8 items-end mb-12 lg:mb-16">
                <div>
                    <span class="eyebrow !text-primary-300" data-reveal>The Capability Matrix</span>
                    <h2 class="headline-display text-4xl sm:text-5xl lg:text-6xl mt-4 max-w-2xl" data-reveal data-reveal-delay="0.05">
                        Every layer of the stack,<br/><span class="text-brand-gradient">under one roof.</span>
                    </h2>
                </div>
                {{-- Filter rail --}}
                <div class="flex flex-wrap gap-2" data-reveal data-reveal-delay="0.1">
                    @foreach ($capCats as $c)
                        <button type="button" @click="cat = '{{ $c }}'"
                            :class="cat === '{{ $c }}' ? 'bg-white text-[#0d0420] border-transparent' : 'border-white/15 text-white/70 hover:text-white hover:border-white/35'"
                            class="px-4 py-2 rounded-full border text-[11px] font-mono uppercase tracking-[0.18em] transition-all duration-300">
                            {{ $c }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Tile grid --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5" data-stagger="0.06">
                @foreach ($capabilities as $cap)
                    <article data-stagger-item data-spotlight
                        x-show="cat === 'All' || cat === '{{ $cap['cat'] }}'"
                        x-transition:enter="transition ease-out duration-400"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="card-spotlight group relative rounded-2xl p-5 lg:p-6 border border-white/10 bg-[#160828]/60 backdrop-blur-xl
                               transition-all duration-500 hover:-translate-y-1.5 hover:border-primary-300/50
                               hover:shadow-[0_24px_60px_-24px_rgba(123,65,179,0.6)]">
                        <div class="relative flex items-start justify-between mb-5">
                            <span class="size-11 grid place-items-center rounded-xl bg-gradient-to-br from-primary-500/25 to-blush-500/10 border border-white/12
                                         transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6">
                                <i data-lucide="{{ $cap['icon'] }}" class="size-5 text-white"></i>
                            </span>
                            <span class="text-[9px] font-mono uppercase tracking-[0.22em] text-white/35">{{ $cap['cat'] }}</span>
                        </div>
                        <h3 class="relative font-display text-lg font-semibold leading-tight">{{ $cap['name'] }}</h3>
                        <p class="relative mt-2 text-sm text-white/55 leading-relaxed">{{ $cap['note'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══ ENGAGEMENT MODELS + FAQ — how to work with us ═══ --}}
    @php
        $models = [
            [
                'tag' => 'Best for validating',
                'name' => 'Discovery Sprint',
                'price' => '1–2 wks', 'unit' => 'fixed scope',
                'desc' => 'A paid, time-boxed sprint that ends in a one-page brief, clickable prototype, and a build estimate.',
                'features' => ['Stakeholder workshops', 'Technical blueprint', 'Clickable prototype', 'Fixed-fee estimate'],
                'popular' => false, 'cta' => 'Start a sprint',
            ],
            [
                'tag' => 'Most popular',
                'name' => 'Embedded Pod',
                'price' => 'Monthly', 'unit' => 'senior pod',
                'desc' => 'A dedicated senior pod — design, engineering and PM — embedded in your team and shipping every week.',
                'features' => ['Dedicated squad', 'Weekly releases', 'Shared Slack + board', 'Pause or scale anytime'],
                'popular' => true, 'cta' => 'Embed a pod',
            ],
            [
                'tag' => 'Best for launches',
                'name' => 'Full Build',
                'price' => 'Project', 'unit' => 'end-to-end',
                'desc' => 'End-to-end ownership from strategy to launch and growth — a fixed roadmap with milestone billing.',
                'features' => ['Strategy → launch', 'Milestone billing', 'QA + CI baked in', '90-day post-launch care'],
                'popular' => false, 'cta' => 'Scope a build',
            ],
        ];
        $faqs = [
            ['q' => 'How much does software development cost with Vowlyn?', 'a' => 'Most engagements start with a fixed-fee discovery sprint. Embedded pods run at a flat monthly rate and full builds are milestone-based. All pricing is transparent before any contract is signed.'],
            ['q' => 'How long does it take to build a web or mobile app?', 'a' => 'Discovery sprints kick off within a week and run about 7 days. Embedded pods spin up in 2–3 weeks once scope is signed; full builds are milestone-based from there.'],
            ['q' => 'Who owns the code and cloud accounts?', 'a' => 'You do. Everything ships to your own repositories and cloud accounts from day one, with no lock-in.'],
            ['q' => 'What is an embedded engineering pod?', 'a' => 'A dedicated senior pod (design, engineering, and PM) embedded in your team and shipping every week at a flat monthly rate, with no timesheets.'],
            ['q' => 'What is included after launch?', 'a' => 'Every full build includes 90 days of post-launch care. Many clients then move to an embedded pod for ongoing growth.'],
        ];
    @endphp
    <section class="relative bg-mesh-light section-vw overflow-hidden">
        <div class="container-vw">
            <div class="max-w-2xl mb-12 lg:mb-16">
                <span class="eyebrow !text-primary-700" data-reveal>Ways to work together</span>
                <h2 class="headline-display text-4xl lg:text-5xl mt-3" data-reveal data-reveal-delay="0.05">
                    Pick the gear that fits your stage.
                </h2>
                <p class="text-on-surface-muted text-lg mt-5 leading-relaxed" data-reveal data-reveal-delay="0.1">
                    Three clear ways to engage — each starts with a conversation, never a contract you can't read in five minutes.
                </p>
            </div>

            {{-- Tiers --}}
            <div class="grid md:grid-cols-3 gap-5 lg:gap-6 mb-20 lg:mb-28" data-stagger="0.1">
                @foreach ($models as $m)
                    <article data-stagger-item
                        class="relative rounded-3xl p-7 lg:p-8 flex flex-col h-full transition-all duration-500 hover:-translate-y-2
                               {{ $m['popular']
                                    ? 'bg-brand-stage text-white border border-white/10 shadow-[0_40px_90px_-30px_rgba(75,0,130,0.5)]'
                                    : 'bg-white border border-on-surface/10 shadow-[0_20px_50px_-24px_rgba(75,0,130,0.18)] hover:shadow-[0_30px_70px_-28px_rgba(75,0,130,0.25)]' }}">
                        @if ($m['popular'])
                            <span class="absolute top-6 right-6 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/15 backdrop-blur-sm text-[9px] font-mono uppercase tracking-[0.22em]">
                                <span class="size-1.5 rounded-full bg-emerald-400 animate-pulse"></span>{{ $m['tag'] }}
                            </span>
                        @else
                            <span class="text-[10px] font-mono uppercase tracking-[0.22em] text-on-surface-muted">{{ $m['tag'] }}</span>
                        @endif

                        <h3 class="font-display text-2xl font-bold mt-3 {{ $m['popular'] ? 'text-white' : 'text-on-surface' }}">{{ $m['name'] }}</h3>
                        <div class="flex items-baseline gap-2 mt-4">
                            <span class="font-display text-4xl font-bold {{ $m['popular'] ? 'text-brand-gradient' : 'text-primary-700' }}">{{ $m['price'] }}</span>
                            <span class="text-xs font-mono uppercase tracking-[0.16em] {{ $m['popular'] ? 'text-white/45' : 'text-on-surface-muted' }}">{{ $m['unit'] }}</span>
                        </div>
                        <p class="mt-4 text-sm leading-relaxed {{ $m['popular'] ? 'text-white/65' : 'text-on-surface-muted' }}">{{ $m['desc'] }}</p>

                        <ul class="mt-6 space-y-3 flex-1">
                            @foreach ($m['features'] as $f)
                                <li class="flex items-center gap-3 text-sm {{ $m['popular'] ? 'text-white/80' : 'text-on-surface' }}">
                                    <span class="size-5 grid place-items-center rounded-full shrink-0 {{ $m['popular'] ? 'bg-white/15' : 'bg-primary-50 text-primary-700' }}">
                                        <i data-lucide="check" class="size-3"></i>
                                    </span>
                                    {{ $f }}
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ url('/#contact') }}" data-magnetic="0.2"
                           class="mt-8 inline-flex items-center justify-center gap-2 px-5 py-3 rounded-full text-sm font-medium transition-colors
                                  {{ $m['popular'] ? 'bg-white text-[#0d0420] hover:bg-white/90' : 'bg-primary-700 text-white hover:bg-primary-800' }}">
                            {{ $m['cta'] }}
                            <i data-lucide="arrow-right" class="size-4"></i>
                        </a>
                    </article>
                @endforeach
            </div>

            {{-- FAQ accordion --}}
            <div class="grid lg:grid-cols-[0.8fr_1.2fr] gap-10 lg:gap-16 items-start">
                <div data-reveal>
                    <span class="eyebrow !text-primary-700">Before you ask</span>
                    <h2 class="headline-display text-3xl lg:text-4xl mt-3">Questions we hear a lot.</h2>
                    <p class="text-on-surface-muted mt-4 leading-relaxed">Still unsure which model fits? A 30-minute call sorts it out faster than any pricing page.</p>
                    <a href="https://calendly.com/junaidswati/new-meeting" target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-6">Book a discovery call</a>

                    {{-- At-a-glance (featured-snippet bait — SEO rewrite, 2026) --}}
                    <x-key-takeaways class="mt-8" :items="[
                        'Six software development services: web, mobile, AI, SaaS, security, cloud DevOps.',
                        'One senior team from strategy to launch — no hand-offs.',
                        'Discovery Sprint · Embedded Pod · Full Build engagement models.',
                        '90-day post-launch care on every full build.',
                        'You own the code and cloud accounts.',
                    ]" />
                </div>

                <div class="divide-y divide-on-surface/10 border-y border-on-surface/10" x-data="{ open: 0 }">
                    @foreach ($faqs as $i => $f)
                        <div data-reveal data-reveal-delay="{{ $i * 0.05 }}">
                            <button type="button" @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                                class="w-full flex items-center justify-between gap-4 py-5 lg:py-6 text-left group">
                                <span class="font-display text-lg lg:text-xl text-on-surface group-hover:text-primary-700 transition-colors">{{ $f['q'] }}</span>
                                <span class="size-8 grid place-items-center rounded-full border border-on-surface/15 shrink-0 transition-all duration-300"
                                      :class="open === {{ $i }} ? 'bg-primary-700 text-white border-transparent rotate-180' : 'text-on-surface'">
                                    <i data-lucide="chevron-down" class="size-4"></i>
                                </span>
                            </button>
                            <div x-show="open === {{ $i }}" x-collapse x-cloak>
                                <p class="pb-6 pr-12 text-on-surface-muted leading-relaxed">{{ $f['a'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
