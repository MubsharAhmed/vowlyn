{{-- ============================================================================
   ABOUT — "Engineered for ambitious founders"
   Pinterest-style asymmetric 3-card row: dark manifesto · portrait showcase · stacked mini-cards.
   ============================================================================ --}}
<section id="about" class="section-vw relative overflow-hidden">

    {{-- Atmosphere --}}
    <div aria-hidden="true" class="absolute inset-0 -z-10 pointer-events-none">
        <div class="absolute top-1/4 -left-32 size-[40rem] rounded-full bg-primary-200/30 blur-3xl"></div>
        <div class="absolute bottom-0 -right-32 size-[36rem] rounded-full bg-blush-200/30 blur-3xl"></div>
    </div>

    <div class="container-vw">

        {{-- Section intro --}}
        <div class="max-w-3xl mb-12 lg:mb-16">
            <div class="eyebrow-row mb-5" data-reveal>
                <span class="eyebrow">Who we are</span>
            </div>
            <h2 class="headline-display text-4xl sm:text-5xl lg:text-6xl text-slate-900 mb-6" data-reveal data-reveal-delay="0.05">
                Engineered for ambitious founders,
                <span class="text-brand-gradient">built to scale.</span>
            </h2>
            <p class="text-lg text-on-surface/65 max-w-2xl leading-relaxed" data-reveal data-reveal-delay="0.12">
                Vowlyn is
                <a href="{{ route('about') }}" class="text-primary-700 font-medium underline decoration-primary-300 underline-offset-4 hover:decoration-primary-600 transition">a small, senior software development studio</a>.
                Strategy, design, engineering, and launch live under one roof — so your product ships fast, stays
                secure, and scales without drama.
            </p>
        </div>

        {{-- ──────────────────────────────────────────────────────────────
             Asymmetric 3-card grid (12-col on desktop):
             [Dark manifesto · 3]  [Portrait showcase · 6]  [Mini stack · 3]
           ─────────────────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-5 lg:gap-6" data-stagger="0.12">

            {{-- ============ CARD 1 — Dark manifesto ============ --}}
            <div data-stagger-item data-tilt data-tilt-max="5"
                 class="lg:col-span-3 bg-brand-stage rounded-[1.75rem] p-7 grain relative overflow-hidden flex flex-col justify-between min-h-[460px] lg:min-h-[520px] border border-white/8">

                <div aria-hidden="true" class="absolute -top-24 -right-16 size-56 rounded-full bg-primary-700/45 blur-3xl pointer-events-none"></div>
                <div aria-hidden="true" class="absolute -bottom-20 -left-16 size-44 rounded-full bg-blush-500/25 blur-3xl pointer-events-none"></div>

                <div class="relative">
                    <div class="size-12 grid place-items-center rounded-2xl bg-white/8 border border-white/14 mb-8 backdrop-blur-sm">
                        <i data-lucide="sparkles" class="size-5 text-white"></i>
                    </div>

                    <p class="text-[10px] font-mono uppercase tracking-[0.18em] text-white/55 mb-4">Our craft</p>
                    <h3 class="font-display text-2xl lg:text-[1.75rem] font-semibold text-white leading-[1.15] mb-5">
                        Editorial precision. Engineering discipline.
                    </h3>
                    <p class="text-sm text-white/65 leading-relaxed">
                        We treat every product like a magazine cover — every pixel intentional, every line of code accountable.
                    </p>
                </div>

                <a href="#process" class="relative inline-flex items-center gap-3 mt-8 text-sm font-medium text-white group">
                    <span class="size-10 rounded-full bg-white text-slate-900 grid place-items-center transition group-hover:bg-primary-300 group-hover:-rotate-12">
                        <i data-lucide="arrow-up-right" class="size-4"></i>
                    </span>
                    <span class="border-b border-white/30 group-hover:border-white pb-0.5 transition-colors">See our process</span>
                </a>
            </div>

            {{-- ============ CARD 2 — Portrait showcase (Pinterest hero card) ============ --}}
            <div data-stagger-item data-tilt data-tilt-max="5"
                 class="lg:col-span-6 bg-gradient-to-br from-white via-lavender-50/60 to-white rounded-[1.75rem] border border-lavender-300/60 p-3 relative overflow-hidden min-h-[460px] lg:min-h-[520px] shadow-[0_24px_60px_-30px_rgba(75,0,130,0.25)]">

                <div class="grid grid-cols-1 sm:grid-cols-[0.95fr_1.05fr] gap-4 h-full">

                    {{-- Portrait frame --}}
                    <div class="relative rounded-[1.25rem] overflow-hidden bg-lavender-100 min-h-[280px] sm:min-h-full">
                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=600&h=750&fit=crop&q=80&auto=format"
                             alt="Vowlyn software developer building a web application"
                             loading="lazy"
                             width="600" height="750"
                             class="absolute inset-0 w-full h-full object-cover object-center" />

                        {{-- Top-left frosted role chip --}}
                        <div class="absolute top-4 left-4 bg-white/85 backdrop-blur-md rounded-full px-3 py-1.5 border border-white/60 flex items-center gap-2">
                            <span class="size-1.5 rounded-full bg-primary-700"></span>
                            <span class="text-[10px] font-mono uppercase tracking-[0.14em] text-slate-900">Senior Strategist</span>
                        </div>

                        {{-- Bottom availability card --}}
                        <div class="absolute bottom-4 left-4 right-4 bg-white/88 backdrop-blur-md rounded-2xl px-4 py-3 flex items-center justify-between border border-white/60 shadow-lg">
                            <div>
                                <p class="text-[10px] font-mono uppercase tracking-[0.14em] text-primary-700/60">Strategy &middot; Engineering</p>
                                <p class="text-sm font-semibold text-slate-900">Currently shipping</p>
                            </div>
                            <span class="relative grid place-items-center size-3" aria-hidden="true">
                                <span class="absolute inset-0 rounded-full bg-emerald-500 animate-ping opacity-70"></span>
                                <span class="relative size-3 rounded-full bg-emerald-500"></span>
                            </span>
                        </div>
                    </div>

                    {{-- Copy side --}}
                    <div class="p-5 sm:p-6 flex flex-col justify-between">
                        <div>
                            <p class="eyebrow mb-4">Embedded, senior teams</p>
                            <h3 class="font-display text-2xl lg:text-[1.875rem] font-semibold text-slate-900 leading-[1.15] mb-5">
                                Working alongside founders, marketing leaders &amp; in-house teams.
                            </h3>
                            <p class="text-sm text-on-surface/65 leading-relaxed mb-6">
                                We help ambitious businesses move faster with digital systems that are
                                <em class="not-italic text-primary-800 font-medium">elegant, secure, and built to scale</em>.
                                No agency theatre — just a small, senior team that ships.
                            </p>
                        </div>

                        {{-- Mini availability strip --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-white border border-lavender-200 px-4 py-3">
                                <p class="text-[10px] font-mono uppercase tracking-[0.12em] text-on-surface/45 mb-1">Avg. response</p>
                                <p class="font-display text-xl font-bold text-slate-900 leading-none">&lt; 24h</p>
                            </div>
                            <div class="rounded-2xl bg-brand-stage text-white px-4 py-3 relative overflow-hidden">
                                <div aria-hidden="true" class="absolute -top-4 -right-4 size-12 rounded-full bg-primary-500/40 blur-xl"></div>
                                <p class="text-[10px] font-mono uppercase tracking-[0.12em] text-white/55 mb-1 relative">2026 capacity</p>
                                <p class="font-display text-xl font-bold leading-none relative">3 slots left</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Floating glass bubble decoration (Pinterest signature) --}}
                <div aria-hidden="true" data-float
                     class="absolute -bottom-6 -left-4 size-12 rounded-full"
                     style="background: radial-gradient(circle at 35% 30%, #fff 0%, rgba(255,255,255,0.5) 45%, rgba(123,65,179,0.55) 100%);
                            box-shadow: inset 0 1px 2px rgba(255,255,255,0.9), 0 14px 28px -6px rgba(75,0,130,0.35);"></div>
            </div>

            {{-- ============ CARD 3 — Stacked mini cards ============ --}}
            <div data-stagger-item class="lg:col-span-3 flex flex-col gap-5 lg:gap-6 min-h-[460px] lg:min-h-[520px]">

                {{-- 3a — Engagement models --}}
                <div class="bg-white rounded-[1.5rem] border border-lavender-300/60 p-5 flex-1 relative overflow-hidden">
                    <div class="flex items-center justify-between mb-5">
                        <p class="text-[10px] font-mono uppercase tracking-[0.16em] text-on-surface/55">Engagement</p>
                        <span class="text-[10px] font-mono text-primary-700 bg-primary-50 rounded-full px-2 py-0.5">2026</span>
                    </div>
                    <ul class="space-y-3.5">
                        @foreach ([
                            ['Strategic sprint', '2–4 wks', 'bg-primary-400'],
                            ['Build partner',    '8–14 wks', 'bg-primary-600'],
                            ['Embedded team',    'Ongoing',  'bg-primary-800'],
                        ] as [$name, $duration, $tone])
                            <li class="flex items-center gap-3">
                                <span class="size-2 rounded-full {{ $tone }} shrink-0"></span>
                                <div class="flex-1 flex items-baseline justify-between gap-3">
                                    <span class="text-sm font-medium text-slate-900">{{ $name }}</span>
                                    <span class="text-[11px] font-mono text-on-surface/55">{{ $duration }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- 3b — Testimonial mini --}}
                <div class="bg-gradient-to-br from-primary-700 via-primary-800 to-slate-900 text-white rounded-[1.5rem] p-5 flex-1 relative overflow-hidden grain">
                    <div aria-hidden="true" class="absolute -top-10 -right-10 size-32 rounded-full bg-blush-500/30 blur-2xl pointer-events-none"></div>

                    <div class="relative flex items-start gap-3 mb-4">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=200&h=200&fit=crop&q=80&auto=format"
                             alt="Daniel Arian, founder of Arian Rugs, a Vowlyn e-commerce client"
                             loading="lazy"
                             width="48" height="48"
                             class="size-12 rounded-full object-cover border-2 border-white/30 shrink-0" />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white truncate">Daniel Arian</p>
                            <p class="text-[10px] font-mono uppercase tracking-[0.12em] text-white/55 truncate">Founder · Arian Rugs</p>
                        </div>
                        <i data-lucide="quote" class="size-4 text-white/35 shrink-0"></i>
                    </div>

                    <p class="relative text-sm text-white/85 leading-relaxed">
                        Vowlyn rebuilt our store with discipline. Conversions up <span class="text-white font-semibold">38%</span> in the first quarter.
                    </p>
                </div>
            </div>
        </div>

        {{-- ============ Bottom signature stat strip ============ --}}
        <div class="mt-10 lg:mt-12 grid grid-cols-2 sm:grid-cols-4 gap-3" data-stagger="0.08">
            @foreach ([
                ['120', '+', 'Products shipped'],
                ['97',  '%', 'Client satisfaction'],
                ['180', '%', 'Avg. growth lift'],
                ['7',   'x', 'Faster GTM cycles'],
            ] as [$num, $unit, $label])
                <div data-stagger-item
                     class="bg-white rounded-2xl border border-lavender-300/50 px-4 py-4 sm:px-5 flex items-center gap-3 sm:gap-4 hover:border-primary-300 hover:shadow-[0_18px_40px_-20px_rgba(75,0,130,0.25)] transition">
                    <div class="flex items-baseline gap-0.5 shrink-0">
                        <span data-counter="{{ $num }}" class="font-display text-3xl sm:text-4xl font-bold text-slate-900 tabular-nums leading-none">0</span>
                        <span class="font-display text-xl sm:text-2xl font-bold text-primary-700 leading-none">{{ $unit }}</span>
                    </div>
                    <p class="text-[11px] font-mono uppercase tracking-[0.12em] text-on-surface/55 leading-tight">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
