{{-- ============================================================================
WHY US — "A Boutique Software Studio, Not an Agency"
Unique decision-stage comparison page (Vowlyn-Why-Us-Page-Rewrite-2026.md,
Option B). Intentionally distinct from the homepage to resolve the
duplicate-content problem: boutique studio vs. typical agency.
============================================================================ --}}
@extends('layouts.app')

@section('title', 'Why Choose Vowlyn — A Boutique Software Studio, Not an Agency')
@section('description', 'Why choose Vowlyn: a senior-only software studio with no middle management, no outsourced contractors, and no lock-in. See how a boutique studio compares to a typical agency.')
@section('canonical', 'https://vowlyn.com/why-us')
@section('og_title', 'Why Choose Vowlyn — A Boutique Software Studio, Not an Agency')
@section('og_description', 'Senior-only, no middle management, no lock-in. See how Vowlyn compares to a typical software agency.')
@section('twitter_title', 'Why Choose Vowlyn — A Boutique Software Studio, Not an Agency')
@section('twitter_description', 'Senior-only, no middle management, no lock-in. See how Vowlyn compares to a typical agency.')

@php
    $comparison = [
        ['Who does the work', 'The senior people who scoped it', 'Junior team after the pitch'],
        ['Team size on your project', 'Small, senior, consistent', 'Rotating, layered'],
        ['Communication', 'Direct with makers', 'Through account managers'],
        ['Code & cloud ownership', 'Yours from day one', 'Often locked to the vendor'],
        ['Contractors', 'None — in-house only', 'Frequently outsourced'],
        ['Speed', 'Fewer hand-offs, faster ships', 'Slowed by internal layers'],
        ['After launch', '90 days care, then optional pod', 'Change-request billing'],
    ];

    $fourReasons = [
        [
            'title' => 'Senior-only, always.',
            'desc' => 'No project is handed to a junior after the sale. The people in the pitch are the people in your repo.',
            'icon' => 'badge-check',
        ],
        [
            'title' => 'You own everything.',
            'desc' => "Code, cloud accounts, and credentials are yours from the first commit — there's no scenario where leaving Vowlyn means losing your product.",
            'icon' => 'key-round',
        ],
        [
            'title' => 'One team, four disciplines.',
            'desc' => 'Strategy, design, engineering, and growth sit in one room, so nothing gets lost in a hand-off between vendors.',
            'icon' => 'users-round',
        ],
        [
            'title' => 'Predictable, not padded.',
            'desc' => 'Fixed-fee builds or a flat monthly pod — milestone billing, no surprise invoices, no timesheet games.',
            'icon' => 'receipt-text',
        ],
    ];

    $whyUsFaqs = [
        [
            'q' => 'Why choose a boutique software studio over an agency?',
            'a' => 'With a boutique studio, the senior people who scope your project are the ones who build it — no hand-offs to junior teams, no account-manager layer, and faster shipping because there are fewer people between you and the code.',
        ],
        [
            'q' => 'What makes Vowlyn different?',
            'a' => 'Vowlyn is senior-only with no outsourced contractors and no middle management. You own all code and cloud accounts from day one, and strategy, design, engineering, and growth all sit on one team.',
        ],
        [
            'q' => 'Will I be locked into Vowlyn?',
            'a' => 'No. Everything ships to your own repositories and cloud accounts, so you can take your product elsewhere at any time. There is no vendor lock-in.',
        ],
        [
            'q' => 'What happens after my project launches?',
            'a' => 'Every full build includes 90 days of post-launch care. Many clients then continue with a flat-rate embedded pod for ongoing growth.',
        ],
    ];
@endphp

@push('head')
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": "WebPage",
      "name": "Why Choose Vowlyn",
      "url": "https://vowlyn.com/why-us",
      "about": { "@id": "https://vowlyn.com/#organization" },
      "primaryImageOfPage": "https://vowlyn.com/brand/vowlyn-og.png",
      "description": "Why choose Vowlyn: a senior-only boutique software studio with no middle management, no outsourced contractors, and no lock-in."
    }
    </script>
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        { "@type": "Question", "name": "Why choose a boutique software studio over an agency?", "acceptedAnswer": { "@type": "Answer", "text": "With a boutique studio, the senior people who scope your project are the ones who build it, with no hand-offs to junior teams and no account-manager layer, so shipping is faster." } },
        { "@type": "Question", "name": "What makes Vowlyn different?", "acceptedAnswer": { "@type": "Answer", "text": "Vowlyn is senior-only with no outsourced contractors and no middle management. You own all code and cloud accounts from day one, and strategy, design, engineering, and growth sit on one team." } },
        { "@type": "Question", "name": "Will I be locked into Vowlyn?", "acceptedAnswer": { "@type": "Answer", "text": "No. Everything ships to your own repositories and cloud accounts, so you can take your product elsewhere at any time. There is no vendor lock-in." } },
        { "@type": "Question", "name": "What happens after my project launches?", "acceptedAnswer": { "@type": "Answer", "text": "Every full build includes 90 days of post-launch care. Many clients then continue with a flat-rate embedded pod for ongoing growth." } }
      ]
    }
    </script>
@endpush

@section('content')
    {{-- ═══ PAGE HERO ═══ --}}
    <section class="relative bg-mesh-light pt-32 lg:pt-44 pb-20 lg:pb-28 overflow-hidden grain">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-32 -left-24 size-[30rem] rounded-full bg-primary-300/30 blur-[120px]"></div>
            <div class="absolute bottom-0 -right-24 size-[26rem] rounded-full bg-blush-300/30 blur-[120px]"></div>
        </div>

        <div class="container-vw relative">
            <div class="max-w-4xl">
                <div class="eyebrow-row mb-6" data-reveal>
                    <span class="h-px w-10 bg-primary-700/40"></span>
                    <span class="eyebrow">Why Vowlyn</span>
                </div>

                <h1 class="headline-display text-5xl sm:text-6xl lg:text-7xl text-slate-900" data-reveal data-reveal-delay="0.05">
                    A Software Studio That Acts Like
                    <span class="text-brand-gradient">Your Own Team.</span>
                </h1>

                <p class="mt-8 text-lg lg:text-xl text-on-surface-muted leading-relaxed max-w-3xl" data-reveal data-reveal-delay="0.12">
                    Most companies choosing a software partner are really choosing between two models: a
                    <strong class="font-semibold text-on-surface">large agency</strong> with layers and hand-offs, or a
                    <strong class="font-semibold text-on-surface">boutique studio</strong> where the senior people who
                    scope your project are the same ones who ship it. Vowlyn is the second kind —
                    <a href="{{ route('about') }}" class="text-primary-700 font-medium underline decoration-primary-300 underline-offset-4 hover:decoration-primary-600 transition">eight senior specialists</a>,
                    no middle management, no lock-in.
                </p>

                <div class="mt-10 flex flex-wrap items-center gap-3.5" data-reveal data-reveal-delay="0.18">
                    <a href="{{ url('/#contact') }}" data-magnetic="0.2" class="btn btn-primary !py-4 !px-7">
                        Start Your Project
                        <i data-lucide="arrow-up-right" class="size-4"></i>
                    </a>
                    <a href="{{ route('portfolio') }}" class="btn btn-outlined !py-4 !px-7">
                        See Our Work
                    </a>
                </div>

                {{-- Quick Answer (GEO) --}}
                <x-quick-answer class="mt-10 max-w-2xl" data-reveal data-reveal-delay="0.22">
                    Choose Vowlyn when you want senior people doing the actual work. It's a boutique, senior-only
                    software studio — no middle-management layer, no outsourced contractors, and no vendor lock-in.
                    The team that scopes your project is the team that ships it, and every line of code and cloud
                    account is yours from day one.
                </x-quick-answer>
            </div>
        </div>
    </section>


    {{-- ═══ STUDIO vs AGENCY — comparison table (the centerpiece) ═══ --}}
    <section class="section-vw bg-surface relative overflow-hidden">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute top-1/3 -right-32 size-[28rem] rounded-full bg-lavender-300/40 blur-3xl"></div>
        </div>

        <div class="container-vw relative">
            <div class="max-w-3xl mb-12 lg:mb-16">
                <div class="eyebrow-row mb-5" data-reveal>
                    <span class="eyebrow">The honest comparison</span>
                </div>
                <h2 class="headline-display text-4xl sm:text-5xl lg:text-6xl text-slate-900" data-reveal data-reveal-delay="0.05">
                    Boutique Software Studio<br /><span class="text-brand-gradient">vs. Typical Agency.</span>
                </h2>
            </div>

            <div class="overflow-x-auto rounded-3xl border border-lavender-300 bg-white shadow-[0_30px_80px_-40px_rgba(75,0,130,0.3)]" data-reveal>
                <table class="w-full min-w-[720px] text-left border-collapse">
                    <caption class="sr-only">Comparison between Vowlyn, a boutique software studio, and a typical agency</caption>
                    <thead>
                        <tr class="border-b border-lavender-300">
                            <th scope="col" class="px-6 py-5 text-[11px] font-mono uppercase tracking-[0.18em] text-on-surface/50 font-medium">
                                What matters to you
                            </th>
                            <th scope="col" class="px-6 py-5 bg-primary-700 text-white">
                                <span class="flex items-center gap-2 font-display text-lg font-semibold">
                                    <i data-lucide="gem" class="size-4"></i>
                                    Vowlyn
                                </span>
                                <span class="block text-[10px] font-mono uppercase tracking-[0.18em] text-white/60 mt-1">Boutique studio</span>
                            </th>
                            <th scope="col" class="px-6 py-5">
                                <span class="block font-display text-lg font-semibold text-on-surface/70">Typical agency</span>
                                <span class="block text-[10px] font-mono uppercase tracking-[0.18em] text-on-surface/40 mt-1">Layers &amp; hand-offs</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-lavender-200">
                        @foreach ($comparison as [$matter, $vowlyn, $agency])
                            <tr class="hover:bg-lavender-50/60 transition-colors">
                                <th scope="row" class="px-6 py-4 text-sm font-semibold text-slate-900">
                                    {{ $matter }}
                                </th>
                                <td class="px-6 py-4 bg-primary-50/70 text-sm text-primary-900 font-medium">
                                    <span class="flex items-start gap-2.5">
                                        <i data-lucide="check" class="size-4 mt-0.5 shrink-0 text-primary-700"></i>
                                        {{ $vowlyn }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-on-surface/60">
                                    <span class="flex items-start gap-2.5">
                                        <i data-lucide="x" class="size-4 mt-0.5 shrink-0 text-on-surface/35"></i>
                                        {{ $agency }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>


    {{-- ═══ FOUR REASONS ═══ --}}
    <section class="section-vw bg-gradient-to-b from-surface to-lavender-100/50 relative overflow-hidden">
        <div class="container-vw relative">
            <div class="max-w-3xl mb-12 lg:mb-16">
                <div class="eyebrow-row mb-5" data-reveal>
                    <span class="eyebrow">Differentiators</span>
                </div>
                <h2 class="headline-display text-4xl sm:text-5xl lg:text-6xl text-slate-900" data-reveal data-reveal-delay="0.05">
                    Four Reasons Teams<br /><span class="text-brand-gradient">Pick Vowlyn.</span>
                </h2>
            </div>

            <div class="grid md:grid-cols-2 gap-5" data-stagger="0.1">
                @foreach ($fourReasons as $i => $r)
                    <article data-stagger-item
                        class="relative group rounded-3xl p-8 lg:p-10 bg-white border border-lavender-300 overflow-hidden hover:border-primary-300 transition-colors duration-500">
                        <span aria-hidden="true"
                            class="absolute -top-4 -right-2 font-display text-[9rem] font-bold leading-none text-lavender-200/60 select-none pointer-events-none">
                            {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <div class="relative flex items-start gap-6">
                            <div class="grid place-items-center size-16 rounded-2xl bg-gradient-to-br from-primary-100 to-lavender-300 text-primary-700 shrink-0 group-hover:scale-110 transition-transform duration-500">
                                <i data-lucide="{{ $r['icon'] }}" class="size-8"></i>
                            </div>
                            <div>
                                <h3 class="font-display text-2xl font-semibold text-slate-900 mb-3">{{ $r['title'] }}</h3>
                                <p class="text-on-surface/65 leading-relaxed">{{ $r['desc'] }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <p class="mt-10 text-on-surface/65 max-w-2xl leading-relaxed" data-reveal>
                Whether you engage for
                <a href="{{ route('services') }}" class="text-primary-700 font-medium underline decoration-primary-300 underline-offset-4 hover:decoration-primary-600 transition">fixed-fee builds or a flat monthly pod</a>,
                the model stays the same: senior people, your repositories, predictable billing.
            </p>
        </div>
    </section>


    {{-- ═══ PROOF — decision-stage testimonials ═══ --}}
    <section class="section-vw bg-brand-stage text-white relative overflow-hidden grain">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-24 left-1/4 size-[26rem] rounded-full bg-primary-700/30 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 size-[24rem] rounded-full bg-blush-500/20 blur-3xl"></div>
        </div>

        <div class="container-vw relative">
            <div class="max-w-3xl mb-12 lg:mb-16">
                <div class="eyebrow-row mb-5" data-reveal>
                    <span class="eyebrow !text-primary-300">Proof</span>
                </div>
                <h2 class="headline-display text-4xl sm:text-5xl lg:text-6xl text-white" data-reveal data-reveal-delay="0.05">
                    Teams That Chose Vowlyn —<br /><span class="text-brand-gradient">and What Changed.</span>
                </h2>
            </div>

            <div class="grid md:grid-cols-2 gap-5 lg:gap-7" data-stagger="0.12">
                @foreach ([
                    [
                        'quote' => 'Vowlyn rebuilt our store with discipline. Conversions are up 38%, and I always knew who was doing the work.',
                        'name' => 'Daniel Arian',
                        'role' => 'Founder, Arian Rugs',
                        'init' => 'DA',
                    ],
                    [
                        'quote' => "Onboarding a new brand used to take weeks. With Vowlyn's platform, it's an afternoon.",
                        'name' => 'Ops Director',
                        'role' => 'United Buying Group',
                        'init' => 'OD',
                    ],
                ] as $t)
                    <figure data-stagger-item class="relative rounded-3xl p-8 lg:p-10 bg-white/[0.04] border border-white/10 backdrop-blur-xl overflow-hidden">
                        <span aria-hidden="true" class="absolute top-3 left-6 font-display text-[7rem] leading-none text-white/10 select-none pointer-events-none">&ldquo;</span>
                        <blockquote class="relative font-display text-xl lg:text-2xl leading-snug text-white font-medium text-pretty mb-8">
                            {{ $t['quote'] }}
                        </blockquote>
                        <figcaption class="relative flex items-center gap-4 pt-6 border-t border-white/10">
                            <div class="size-12 rounded-full bg-gradient-to-br from-primary-400 to-blush-500 grid place-items-center text-white font-display font-bold shrink-0">
                                {{ $t['init'] }}
                            </div>
                            <div>
                                <p class="font-semibold text-white">{{ $t['name'] }}</p>
                                <p class="text-sm text-white/55">{{ $t['role'] }}</p>
                            </div>
                        </figcaption>
                    </figure>
                @endforeach
            </div>

            <p class="mt-10 text-white/65 leading-relaxed" data-reveal>
                Numbers first, adjectives second — read the
                <a href="{{ route('portfolio') }}" class="text-primary-300 font-medium underline decoration-primary-300/50 underline-offset-4 hover:decoration-primary-300 transition">full case studies in the portfolio</a>.
            </p>
        </div>
    </section>


    {{-- ═══ FAQ + KEY TAKEAWAYS ═══ --}}
    <section aria-labelledby="paa-whyus" class="section-vw bg-surface relative overflow-hidden">
        <div class="container-vw relative">
            <div class="grid lg:grid-cols-[0.8fr_1.2fr] gap-10 lg:gap-16 items-start">
                <div data-reveal>
                    <span class="eyebrow !text-primary-700">People also ask</span>
                    <h2 id="paa-whyus" class="headline-display text-3xl lg:text-4xl mt-3 text-slate-900">
                        Why Vowlyn — common questions.
                    </h2>
                    <p class="text-on-surface-muted mt-4 leading-relaxed">
                        Comparing studios and agencies? These are the questions decision-stage teams ask us most.
                    </p>

                    <x-key-takeaways class="mt-8" :items="[
                        'Vowlyn is a senior-only boutique software studio — not a layered agency.',
                        'The team that scopes your project is the team that ships it.',
                        'You own all code and cloud accounts from day one; no lock-in.',
                        'Fixed-fee or flat monthly pricing, with 90 days of post-launch care.',
                    ]" />
                </div>

                <x-faq-accordion :items="$whyUsFaqs" />
            </div>
        </div>
    </section>

    {{-- ═══ FINAL CTA (unique) ═══ --}}
    <section class="relative bg-mesh-light py-24 lg:py-32 overflow-hidden grain">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-32 -left-24 size-[28rem] rounded-full bg-primary-300/30 blur-[120px]"></div>
            <div class="absolute -top-24 -right-24 size-[24rem] rounded-full bg-blush-300/35 blur-[120px]"></div>
        </div>

        <div class="container-vw relative max-w-4xl text-center">
            <p class="eyebrow !text-primary-700 mb-5" data-reveal>The honest next step</p>
            <h2 class="headline-display text-4xl sm:text-5xl lg:text-6xl text-slate-900" data-reveal data-reveal-delay="0.05">
                See Whether Vowlyn Is the<br /><span class="text-brand-gradient">Right Room for You.</span>
            </h2>
            <p class="mt-8 text-lg text-on-surface-muted max-w-xl mx-auto leading-relaxed" data-reveal data-reveal-delay="0.1">
                Book a 30-minute call. We'll be honest about whether your project is a fit — and if it isn't,
                we'll point you somewhere better.
            </p>
            <div class="mt-10 flex items-center justify-center gap-4 flex-wrap" data-reveal data-reveal-delay="0.15">
                <a href="https://calendly.com/junaidswati/new-meeting" target="_blank" rel="noopener noreferrer" data-magnetic="0.2" class="btn btn-primary !py-4 !px-7">
                    Book a 30-minute call
                    <i data-lucide="arrow-up-right" class="size-4"></i>
                </a>
                <a href="{{ route('about') }}" class="inline-flex items-center gap-2 text-sm font-mono uppercase tracking-[0.18em] text-on-surface hover:text-primary-700 transition-colors">
                    Meet the team <i data-lucide="arrow-right" class="size-4"></i>
                </a>
            </div>
        </div>
    </section>
@endsection
