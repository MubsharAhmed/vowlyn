{{-- ============================================================================
   HERO — "We Build Digital Products That Scale"
   Pinterest-inspired: light lavender atmosphere + floating CSS-glass primitives
   + signature dark "Execution Pulse" card cutting into the bottom of the hero.
   ============================================================================ --}}
<section id="home" class="relative pt-32 lg:pt-40 pb-28 overflow-hidden">

    {{-- Decorative aurora & grid -------------------------------------------- --}}
    <div aria-hidden="true" class="absolute inset-0 -z-10 pointer-events-none">
        {{-- Subtle 8px line grid --}}
        <div class="absolute inset-0 opacity-[0.04]"
             style="background-image: linear-gradient(rgba(75,0,130,1) 1px, transparent 1px), linear-gradient(90deg, rgba(75,0,130,1) 1px, transparent 1px); background-size: 80px 80px;"></div>
        {{-- Aurora blobs --}}
        <div class="absolute -top-32 -left-20 size-[36rem] rounded-full bg-primary-300/35 blur-3xl"></div>
        <div class="absolute top-20 -right-32 size-[40rem] rounded-full bg-blush-200/50 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 size-[28rem] rounded-full bg-lavender-300/40 blur-3xl"></div>
    </div>

    <div class="container-vw relative">
        <div class="grid lg:grid-cols-[1.05fr_1fr] gap-12 lg:gap-16 items-center">

            {{-- LEFT: copy -------------------------------------------------- --}}
            <div class="relative">
                {{-- Eyebrow pill --}}
                <div class="inline-flex items-center gap-2.5 pill-dark rounded-full pl-2 pr-4 py-1.5 mb-7 text-xs" data-reveal>
                    <span class="grid place-items-center size-6 rounded-full bg-gradient-to-br from-primary-400 to-blush-400">
                        <i data-lucide="sparkles" class="size-3 text-white"></i>
                    </span>
                    <span class="font-mono uppercase tracking-[0.16em] text-white/85">Future-ready product engineering</span>
                </div>

                {{-- Headline --}}
                <h1 class="headline-display text-5xl sm:text-6xl lg:text-[5.25rem] xl:text-[5.75rem] mb-7 text-slate-900" data-reveal data-reveal-delay="0.08">
                    We Build
                    <span class="relative inline-block">
                        Digital&nbsp;Products
                        <svg aria-hidden="true" viewBox="0 0 400 14" class="absolute -bottom-2 left-0 w-full h-3 text-primary-700/40" preserveAspectRatio="none">
                            <path d="M 2 8 Q 100 2, 200 7 T 398 6" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" />
                        </svg>
                    </span>
                    <br />
                    That <span class="text-brand-gradient">Scale.</span>
                </h1>

                {{-- Sub --}}
                <p class="text-lg sm:text-xl text-on-surface/65 max-w-xl leading-relaxed mb-9" data-reveal data-reveal-delay="0.18">
                    Vowlyn helps businesses launch modern web platforms, mobile apps, AI-powered solutions, and scalable SaaS systems that drive real growth.
                </p>

                {{-- CTAs --}}
                <div class="flex flex-wrap items-center gap-3.5 mb-12" data-reveal data-reveal-delay="0.26">
                    <a href="#contact" data-magnetic="0.2" class="btn btn-primary !py-4 !px-7">
                        Start Your Project
                        <i data-lucide="arrow-up-right" class="size-4"></i>
                    </a>
                    <a href="#projects" class="btn btn-outlined !py-4 !px-7">
                        <i data-lucide="play-circle" class="size-4"></i>
                        View Projects
                    </a>
                </div>

                {{-- Hero stats row --}}
                <div class="flex flex-wrap gap-x-10 gap-y-5" data-stagger="0.12">
                    @foreach ([
                        ['120', '+', 'Products Shipped'],
                        ['97', '%', 'Client Satisfaction'],
                        ['7', 'x', 'Faster Delivery'],
                    ] as [$num, $suffix, $label])
                        <div data-stagger-item>
                            <div class="flex items-baseline gap-0.5">
                                <span data-counter="{{ $num }}" class="font-display text-4xl sm:text-5xl font-bold text-slate-900 tabular-nums">0</span>
                                <span class="font-display text-3xl font-bold text-primary-700">{{ $suffix }}</span>
                            </div>
                            <p class="text-xs font-mono uppercase tracking-[0.14em] text-on-surface/55 mt-1.5">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- RIGHT: 3D glass platter composition -------------------------
                 Architecture (back → front):
                  1. Ambient ground shadow
                  2. Platter side (thickness band — darker)
                  3. Platter top (elliptical glass disc)
                  4. Cylinders standing on the platter
                  5. Floating glass app-icon cubes (above the scene)
                  6. Floating UI chip + bubbles (decorative)
            ----------------------------------------------------------------- --}}
            <div data-hero-scene class="relative h-[520px] sm:h-[620px] lg:h-[680px] scene-3d" aria-hidden="true">

                {{-- ── 1. Ambient ground shadow ── --}}
                <div class="absolute bottom-[6%] left-[10%] right-[10%] h-16 rounded-[50%]"
                     style="background: radial-gradient(ellipse at center, rgba(75,0,130,0.32) 0%, transparent 70%); filter: blur(28px);"></div>

                {{-- ── 2. Platter side (thickness / depth band) ── --}}
                <div data-platter class="absolute bottom-[14%] left-[5%] right-[5%]"
                     style="height: 70px;
                            background: linear-gradient(180deg, rgba(75,0,130,0.45) 0%, rgba(75,0,130,0.85) 55%, rgba(75,0,130,0.5) 100%);
                            border-radius: 50%;
                            transform: scaleY(0.45);
                            transform-origin: top;
                            box-shadow: 0 12px 24px rgba(75,0,130,0.25);"></div>

                {{-- ── 3. Platter top — the glass ellipse ── --}}
                <div data-platter class="absolute bottom-[16%] left-[5%] right-[5%]"
                     style="aspect-ratio: 2.7 / 1;
                            background:
                              radial-gradient(ellipse at 50% 25%, rgba(255,255,255,0.95) 0%, rgba(230,230,250,0.75) 50%, rgba(186,126,244,0.3) 100%);
                            border-radius: 50%;
                            border: 1px solid rgba(255,255,255,0.7);
                            box-shadow:
                              inset 0 -12px 24px rgba(75,0,130,0.18),
                              inset 0 1px 0 rgba(255,255,255,0.95),
                              inset 4px 0 12px rgba(255,255,255,0.4);"></div>

                {{-- Reflective rim crescent on the platter front edge --}}
                <div data-platter class="absolute bottom-[15%] left-[8%] right-[8%]"
                     style="height: 10px;
                            background: linear-gradient(90deg, transparent 5%, rgba(255,255,255,0.85) 50%, transparent 95%);
                            border-radius: 50%;
                            filter: blur(2px);"></div>

                {{-- ── 4a. Glass cylinder — TALL VIOLET (back-left on platter) ── --}}
                <div data-cylinder
                     class="absolute bottom-[24%] left-[24%] w-24 h-60 rounded-full"
                     style="background:
                          linear-gradient(180deg, rgba(255,255,255,0.45) 0%, rgba(123,65,179,0.6) 35%, rgba(75,0,130,0.78) 100%);
                        box-shadow:
                          inset 7px 0 10px rgba(255,255,255,0.5),
                          inset -7px 0 14px rgba(75,0,130,0.4),
                          inset 0 -12px 22px rgba(75,0,130,0.45),
                          0 32px 40px -18px rgba(75,0,130,0.55);
                        backdrop-filter: blur(2px);">
                    <div class="absolute top-3 left-1/2 -translate-x-1/2 w-2 h-14 rounded-full bg-white/65 blur-[2px]"></div>
                    <div class="absolute top-3 right-3 w-1 h-8 rounded-full bg-white/45 blur-[1px]"></div>
                </div>

                {{-- ── 4b. Glass cylinder — SHORT PINK (center-left on platter) ── --}}
                <div data-cylinder
                     class="absolute bottom-[24%] left-[44%] w-20 h-44 rounded-full"
                     style="background:
                          linear-gradient(180deg, rgba(255,255,255,0.55) 0%, rgba(255,209,220,0.7) 35%, rgba(200,69,155,0.6) 100%);
                        box-shadow:
                          inset 6px 0 9px rgba(255,255,255,0.55),
                          inset -6px 0 12px rgba(200,69,155,0.35),
                          inset 0 -12px 22px rgba(200,69,155,0.4),
                          0 26px 36px -16px rgba(200,69,155,0.45);">
                    <div class="absolute top-2 left-1/2 -translate-x-1/2 w-1.5 h-10 rounded-full bg-white/70 blur-[1px]"></div>
                </div>

                {{-- ── 4c. Glass cylinder — MEDIUM CYAN (back-right on platter) ── --}}
                <div data-cylinder
                     class="absolute bottom-[24%] left-[62%] w-16 h-52 rounded-full"
                     style="background:
                          linear-gradient(180deg, rgba(255,255,255,0.5) 0%, rgba(24,210,255,0.45) 35%, rgba(92,124,245,0.6) 100%);
                        box-shadow:
                          inset 5px 0 8px rgba(255,255,255,0.55),
                          inset -5px 0 10px rgba(24,210,255,0.3),
                          inset 0 -10px 20px rgba(92,124,245,0.35),
                          0 24px 34px -16px rgba(92,124,245,0.45);">
                    <div class="absolute top-2 left-1/2 -translate-x-1/2 w-1.5 h-10 rounded-full bg-white/70 blur-[1px]"></div>
                </div>

                {{-- Tiny reflections under cylinder bases on platter (depth glue) --}}
                <div class="absolute bottom-[22%] left-[24%] w-24 h-2 rounded-full bg-white/55 blur-[3px]"></div>
                <div class="absolute bottom-[22%] left-[44%] w-20 h-2 rounded-full bg-white/55 blur-[3px]"></div>
                <div class="absolute bottom-[22%] left-[62%] w-16 h-2 rounded-full bg-white/55 blur-[3px]"></div>

                {{-- ── 5a. Glass cube — CODE icon (top-right) ── --}}
                <div data-cube
                     class="absolute top-[4%] right-[18%] size-24 rounded-3xl grid place-items-center"
                     style="background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(186,126,244,0.5) 100%);
                            box-shadow:
                              inset 0 1px 0 rgba(255,255,255,0.95),
                              inset 0 -10px 22px rgba(75,0,130,0.22),
                              0 26px 50px -16px rgba(75,0,130,0.4);
                            backdrop-filter: blur(8px);">
                    <i data-lucide="code-2" class="size-10 text-primary-700"></i>
                </div>

                {{-- ── 5b. Glass cube — BOT icon (top-right far) ── --}}
                <div data-cube
                     class="absolute top-[6%] right-[1%] size-20 rounded-3xl grid place-items-center"
                     style="background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,209,220,0.7) 100%);
                            box-shadow:
                              inset 0 1px 0 rgba(255,255,255,0.95),
                              inset 0 -10px 22px rgba(200,69,155,0.22),
                              0 24px 44px -14px rgba(200,69,155,0.35);
                            backdrop-filter: blur(8px);">
                    <i data-lucide="bot" class="size-9 text-blush-600"></i>
                </div>

                {{-- ── 5c. Glass cube — SPARKLE (top-center small) ── --}}
                <div data-cube
                     class="absolute top-[2%] left-[28%] size-16 rounded-2xl grid place-items-center"
                     style="background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(230,230,250,0.7) 100%);
                            box-shadow:
                              inset 0 1px 0 rgba(255,255,255,1),
                              inset 0 -8px 14px rgba(75,0,130,0.15),
                              0 18px 34px -12px rgba(75,0,130,0.3);">
                    <i data-lucide="sparkles" class="size-7 text-primary-700"></i>
                </div>

                {{-- ── 6a. Floating glass bubbles ── --}}
                @foreach ([['10%','10%','size-6'], ['38%','82%','size-4'], ['12%','60%','size-3'], ['82%','12%','size-5']] as [$t, $l, $sz])
                    <div data-float
                         class="absolute {{ $sz }} rounded-full"
                         style="top: {{ $t }}; left: {{ $l }};
                                background: radial-gradient(circle at 35% 30%, #fff 0%, rgba(255,255,255,0.5) 45%, rgba(123,65,179,0.6) 100%);
                                box-shadow: inset 0 1px 2px rgba(255,255,255,0.9), 0 8px 16px -4px rgba(75,0,130,0.3);">
                    </div>
                @endforeach

                {{-- ── 6b. Floating "Launch Speed" UI chip ── --}}
                <div data-chip
                     class="absolute top-[18%] right-[5%] pill-dark rounded-2xl px-3.5 py-2.5 flex items-center gap-2.5 shadow-lg">
                    <span class="grid place-items-center size-7 rounded-full bg-gradient-to-br from-primary-400 to-blush-400">
                        <i data-lucide="zap" class="size-3.5 text-white"></i>
                    </span>
                    <div class="text-left">
                        <p class="text-[10px] font-mono uppercase tracking-[0.14em] text-white/55">Launch Speed</p>
                        <p class="text-sm font-semibold text-white">3.2x Faster</p>
                    </div>
                </div>

                {{-- ── 6c. Floating "+" pill (Pinterest signature) ── --}}
                <div data-chip
                     class="absolute top-[14%] right-[28%] size-11 rounded-full grid place-items-center"
                     style="background: linear-gradient(135deg, #7b41b3 0%, #4b0082 100%);
                            box-shadow: 0 12px 28px -8px rgba(75,0,130,0.55), inset 0 1px 0 rgba(255,255,255,0.3);">
                    <i data-lucide="plus" class="size-5 text-white"></i>
                </div>
            </div>
        </div>

        {{-- ─── Bottom: "Execution Pulse" live ops console ─── --}}
        @php
            $pulseMetrics = [
                [
                    'label' => 'Product Velocity',
                    'sub' => 'Sprints shipped / week',
                    'value' => 94,
                    'decimals' => 0,
                    'unit' => '%',
                    'trend' => '+2.4',
                    'period' => 'Last 24h',
                    'gradientId' => 'pulse-velocity',
                    'stops' => [['offset' => '0%', 'color' => '#c8459b'], ['offset' => '100%', 'color' => '#7b41b3']],
                    'line' => 'M0,38 L18,32 L36,40 L54,22 L72,28 L90,14 L108,26 L126,10 L144,20 L162,8 L180,16 L200,6',
                ],
                [
                    'label' => 'Infrastructure Stability',
                    'sub' => 'Uptime · last 90 days',
                    'value' => 99.99,
                    'decimals' => 2,
                    'unit' => '%',
                    'trend' => '+0.01',
                    'period' => 'Last 90d',
                    'gradientId' => 'pulse-stability',
                    'stops' => [['offset' => '0%', 'color' => '#ff7ab8'], ['offset' => '100%', 'color' => '#ef4444']],
                    'line' => 'M0,8 L25,7 L50,9 L70,18 L82,16 L100,6 L125,5 L150,7 L175,5 L200,4',
                ],
                [
                    'label' => 'AI Automation Coverage',
                    'sub' => 'Workflows orchestrated',
                    'value' => 73,
                    'decimals' => 0,
                    'unit' => '%',
                    'trend' => '+8.6',
                    'period' => 'Last 30d',
                    'gradientId' => 'pulse-ai',
                    'stops' => [['offset' => '0%', 'color' => '#18d2ff'], ['offset' => '100%', 'color' => '#5c7cf5']],
                    'line' => 'M0,42 L22,38 L44,34 L66,32 L88,26 L110,22 L132,18 L154,22 L176,14 L200,12',
                ],
            ];
        @endphp

        <div class="mt-16 lg:mt-20 relative" data-reveal data-reveal-delay="0.1">
            <div class="bg-surface-ink rounded-3xl p-6 sm:p-8 lg:p-10 grain relative overflow-hidden border border-white/10 shadow-xl">

                {{-- Decorative gradient blobs --}}
                <div aria-hidden="true"
                    class="absolute -top-32 -left-20 size-80 rounded-full bg-primary-700/40 blur-3xl pointer-events-none"></div>
                <div aria-hidden="true"
                    class="absolute -bottom-24 right-0 size-72 rounded-full bg-blush-500/20 blur-3xl pointer-events-none"></div>

                {{-- ═══ Top status bar — live ops header ═══ --}}
                <div class="relative flex flex-wrap items-center justify-between gap-4 pb-5 mb-7 border-b border-white/10">
                    <div class="flex items-center gap-4 min-w-0">
                        {{-- Pulsing live dot --}}
                        <span class="relative grid place-items-center size-2.5 shrink-0">
                            <span class="absolute inset-0 rounded-full bg-emerald-400 animate-ping opacity-70"></span>
                            <span class="relative size-2.5 rounded-full bg-emerald-400 shadow-[0_0_12px_rgba(52,211,153,0.8)]"></span>
                        </span>
                        <p class="eyebrow !text-white/75 whitespace-nowrap">Execution Pulse — Realtime</p>
                        <span aria-hidden="true" class="hidden md:block h-3 w-px bg-white/15"></span>
                        <span class="hidden md:inline text-[10px] font-mono uppercase tracking-[0.15em] text-white/40 whitespace-nowrap truncate">
                            Synced 2s ago &nbsp;·&nbsp; 14 active workflows
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- Continuous EKG ticker --}}
                        <svg viewBox="0 0 120 24" class="hidden lg:block w-32 h-6 text-white/40" preserveAspectRatio="none" aria-hidden="true">
                            <defs>
                                <linearGradient id="pulse-ekg" x1="0" x2="1" y1="0" y2="0">
                                    <stop offset="0%" stop-color="#7b41b3"/>
                                    <stop offset="50%" stop-color="#c8459b"/>
                                    <stop offset="100%" stop-color="#ff7ab8"/>
                                </linearGradient>
                            </defs>
                            <path d="M0,12 L18,12 L24,4 L30,20 L36,8 L44,12 L66,12 L72,6 L78,18 L84,12 L120,12"
                                fill="none" stroke="url(#pulse-ekg)" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"
                                data-ekg/>
                        </svg>

                        <div class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 bg-emerald-500/10 border border-emerald-400/25">
                            <span class="size-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-[10px] font-mono uppercase tracking-[0.18em] text-emerald-300">
                                All systems nominal
                            </span>
                        </div>
                    </div>
                </div>

                {{-- ═══ Metric tiles ═══ --}}
                <div class="relative grid sm:grid-cols-3 gap-5 lg:gap-7">
                    @foreach ($pulseMetrics as $m)
                        <div data-stagger-item
                            class="group relative p-4 lg:p-5 rounded-2xl bg-white/[0.03] border border-white/[0.06] transition-all duration-500 hover:bg-white/[0.06] hover:border-white/15">

                            {{-- Top: label + trend pill --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="min-w-0">
                                    <p class="text-[10px] font-mono uppercase tracking-[0.18em] text-white/50 leading-tight">{{ $m['label'] }}</p>
                                    <p class="mt-1 text-[10px] text-white/35">{{ $m['sub'] }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1 text-[10px] font-mono px-2 py-0.5 rounded-full bg-emerald-400/12 text-emerald-300 border border-emerald-400/20 shrink-0">
                                    <i data-lucide="trending-up" class="size-2.5"></i>
                                    {{ $m['trend'] }}%
                                </span>
                            </div>

                            {{-- Big counter --}}
                            <div class="flex items-baseline gap-1 mb-3">
                                <span data-counter="{{ $m['value'] }}"
                                    data-counter-decimals="{{ $m['decimals'] }}"
                                    class="font-display text-4xl lg:text-5xl font-bold text-white tabular-nums leading-none">0</span>
                                <span class="font-display text-xl lg:text-2xl font-bold text-white/40 leading-none">{{ $m['unit'] }}</span>
                            </div>

                            {{-- Animated sparkline mini-chart --}}
                            <svg viewBox="0 0 200 50" class="w-full h-12 mt-1 mb-2" preserveAspectRatio="none" aria-hidden="true">
                                <defs>
                                    <linearGradient id="{{ $m['gradientId'] }}" x1="0" x2="1" y1="0" y2="0">
                                        @foreach ($m['stops'] as $s)
                                            <stop offset="{{ $s['offset'] }}" stop-color="{{ $s['color'] }}"/>
                                        @endforeach
                                    </linearGradient>
                                    <linearGradient id="{{ $m['gradientId'] }}-fill" x1="0" x2="0" y1="0" y2="1">
                                        <stop offset="0%" stop-color="{{ $m['stops'][0]['color'] }}" stop-opacity="0.4"/>
                                        <stop offset="100%" stop-color="{{ $m['stops'][1]['color'] }}" stop-opacity="0"/>
                                    </linearGradient>
                                </defs>

                                {{-- Subtle dotted baseline --}}
                                <line x1="0" y1="48" x2="200" y2="48" stroke="rgba(255,255,255,0.07)" stroke-width="1" stroke-dasharray="2 3"/>

                                {{-- Area fill (fades in) --}}
                                <path d="{{ $m['line'] }} L200,50 L0,50 Z"
                                    fill="url(#{{ $m['gradientId'] }}-fill)"
                                    data-sparkline-area/>

                                {{-- Line stroke (draws in) --}}
                                <path d="{{ $m['line'] }}"
                                    fill="none"
                                    stroke="url(#{{ $m['gradientId'] }})"
                                    stroke-width="1.75"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    data-sparkline-stroke/>

                                {{-- Last data point — pulsing dot --}}
                                <circle cx="200" cy="{{ str_contains($m['line'], 'L200,4') ? 4 : (str_contains($m['line'], 'L200,12') ? 12 : 6) }}"
                                    r="2.5" fill="white" class="animate-pulse"/>
                                <circle cx="200" cy="{{ str_contains($m['line'], 'L200,4') ? 4 : (str_contains($m['line'], 'L200,12') ? 12 : 6) }}"
                                    r="5" fill="{{ $m['stops'][0]['color'] }}" fill-opacity="0.25"/>
                            </svg>

                            {{-- Footer: period + LIVE dot --}}
                            <div class="flex items-center justify-between text-[10px] font-mono uppercase tracking-[0.16em] text-white/35">
                                <span>{{ $m['period'] }}</span>
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="size-1 rounded-full bg-emerald-400 animate-pulse"></span>
                                    Live
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
