{{-- ============================================================================
SERVICES — "End-to-End Digital Execution for High-Growth Teams"
Asymmetric bento grid with cursor-tracked spotlights, capability chips,
animated number watermarks, and an infinite expertise marquee strip.
============================================================================ --}}
@php
    $services = [
        [
            'num' => '01',
            'title' => 'Modern Web Apps',
            'desc' => 'Conversion-focused, edge-rendered web platforms built with Next.js and headless CMS — SEO-clean, fast, and engineered to scale.',
            'icon' => 'monitor',
            'wide' => true,
            'tags' => ['Next.js', 'React', 'Edge', 'Performance', 'SSR'],
        ],
        [
            'num' => '02',
            'title' => 'Mobile Engineering',
            'desc' => 'React Native and native iOS/Android apps shipped to both stores from one codebase, with offline-first architecture.',
            'icon' => 'smartphone',
            'wide' => false,
            'tags' => ['iOS', 'Android', 'React Native'],
        ],
        [
            'num' => '03',
            'title' => 'AI Integration',
            'desc' => 'RAG assistants, LLM pipelines, and computer-vision systems wired into your product with measurable ROI.',
            'icon' => 'sparkles',
            'wide' => false,
            'tags' => ['LLMs', 'RAG', 'Agents', 'Automation'],
        ],
        [
            'num' => '04',
            'title' => 'Scalable SaaS',
            'desc' => 'Multi-tenant SaaS platforms with subscription billing, RBAC, and observability baked in from day one.',
            'icon' => 'layers',
            'wide' => false,
            'tags' => ['Multi-tenant', 'Billing', 'RBAC'],
        ],
        [
            'num' => '05',
            'title' => 'Enterprise Security',
            'desc' => 'SOC 2-aligned hardening, SSO/SAML, secrets hygiene, and compliance-ready workflows.',
            'icon' => 'shield-check',
            'wide' => true,
            'tags' => ['SOC 2', 'SSO / SAML', 'Audits', 'Compliance', 'Zero-trust'],
        ],
        [
            'num' => '06',
            'title' => 'Cloud DevOps',
            'desc' => 'AWS/GCP infrastructure with IaC, CI/CD pipelines, and 24/7 observability — infrastructure that ships itself.',
            'icon' => 'cloud-cog',
            'wide' => false,
            'tags' => ['AWS', 'IaC', 'CI/CD'],
        ],
    ];

    $marquee = ['Products', 'Platforms', 'Experiences', 'Commerce', 'AI', 'Mobile', 'Cloud', 'Design Systems', 'Growth'];
@endphp

<section id="services" class="section-vw relative overflow-hidden">
    {{-- Decorative orbs --}}
    <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
        <div class="absolute top-40 -left-32 size-[28rem] rounded-full bg-primary-200/40 blur-3xl"></div>
        <div class="absolute bottom-40 -right-32 size-[24rem] rounded-full bg-blush-200/50 blur-3xl"></div>
    </div>

    <div class="container-vw relative">
        {{-- ============ Header ============ --}}
        <div class="grid lg:grid-cols-[1.2fr_1fr] gap-10 lg:gap-16 items-end mb-14 lg:mb-20">
            <div>
                <div class="eyebrow-row mb-5" data-reveal>
                    <span class="eyebrow">Services</span>
                </div>
                <h2 class="headline-display text-4xl sm:text-5xl lg:text-6xl text-slate-900" data-reveal
                    data-reveal-delay="0.05">
                    Software Development Services<br />
                    for <span class="text-brand-gradient">High-Growth Teams</span>
                </h2>
            </div>

            <div class="space-y-5 max-w-md lg:justify-self-end" data-reveal data-reveal-delay="0.12">
                <p class="text-on-surface/65 text-lg leading-relaxed">
                    Software development services from one senior studio —
                    <a href="{{ route('services') }}" class="text-primary-700 font-medium underline decoration-primary-300 underline-offset-4 hover:decoration-primary-600 transition">modern web apps</a>,
                    mobile engineering, AI integration,
                    <a href="{{ route('services') }}" class="text-primary-700 font-medium underline decoration-primary-300 underline-offset-4 hover:decoration-primary-600 transition">SaaS platforms</a>,
                    enterprise security, and cloud DevOps — strategy to launch under one roof.
                </p>
                <div class="flex flex-wrap items-center gap-3 pt-1">
                    <a href="#contact"
                        class="group inline-flex items-center gap-2 text-sm font-medium px-5 py-3 rounded-full bg-slate-900 text-white hover:bg-primary-700 transition-colors duration-300">
                        Start a project
                        <i data-lucide="arrow-up-right"
                            class="size-4 transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5"></i>
                    </a>
                    <span class="text-xs font-mono uppercase tracking-[0.18em] text-slate-400">
                        ◌ Currently booking Q3
                    </span>
                </div>
            </div>
        </div>

        {{-- ============ Bento grid ============ --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5" data-stagger="0.08">
            @foreach ($services as $service)
                <article data-stagger-item data-spotlight @class([
                    'group relative rounded-3xl overflow-hidden card-spotlight transition-all duration-500',
                    'bg-white/70 backdrop-blur-xl border border-lavender-300/70',
                    'hover:-translate-y-2 hover:border-primary-300/70 hover:shadow-[0_30px_70px_-25px_rgba(75,0,130,0.35)]',
                    'lg:col-span-2' => $service['wide'],
                ])>
                    {{-- Animated gradient hairline at top --}}
                    <div aria-hidden="true"
                        class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-primary-400/0 to-transparent group-hover:via-primary-500/70 transition-all duration-500">
                    </div>

                    {{-- Huge background numeral (watermark) --}}
                    <div aria-hidden="true" class="absolute -bottom-8 -right-2 font-display font-black leading-none select-none pointer-events-none
                                       text-[8rem] lg:text-[10rem]
                                       bg-gradient-to-br from-slate-900/[0.05] to-slate-900/[0.01] bg-clip-text text-transparent
                                       transition-all duration-500
                                       group-hover:from-primary-500/30 group-hover:to-blush-500/15">
                        {{ $service['num'] }}
                    </div>

                    {{-- Card content --}}
                    <div class="relative z-10 p-6 lg:p-8 h-full flex flex-col">
                        {{-- Top row: icon halo + (Featured tag if wide) --}}
                        <div class="flex items-start justify-between mb-6 lg:mb-7">
                            <div class="relative shrink-0 grid place-items-center size-14 rounded-2xl
                                                bg-lavender-200 text-primary-700
                                                transition-all duration-500
                                                group-hover:scale-110 group-hover:rotate-[6deg]
                                                group-hover:bg-gradient-to-br group-hover:from-primary-600 group-hover:to-blush-500
                                                group-hover:text-white
                                                group-hover:shadow-[0_12px_30px_-8px_rgba(123,65,179,0.55)]">
                                <i data-lucide="{{ $service['icon'] }}" class="size-7 relative z-10"></i>
                                {{-- Pulse halo --}}
                                <div aria-hidden="true"
                                    class="absolute inset-0 rounded-2xl bg-primary-400/0 group-hover:bg-primary-400/30 blur-lg transition-all duration-500">
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                @if ($service['wide'])
                                    <span
                                        class="inline-flex items-center gap-1.5 text-[10px] font-mono uppercase tracking-[0.18em] px-2.5 py-1 rounded-full bg-primary-50 text-primary-700 border border-primary-200/70">
                                        <span class="size-1 rounded-full bg-primary-500 animate-pulse"></span>
                                        Featured
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Title --}}
                        <h3 @class([
                            'font-display font-semibold text-slate-900 mb-3 leading-tight',
                            'text-2xl lg:text-3xl' => $service['wide'],
                            'text-2xl' => !$service['wide'],
                        ])>
                            {{ $service['title'] }}
                        </h3>

                        {{-- Description --}}
                        <p @class([
                            'text-on-surface/65 leading-relaxed mb-6',
                            'max-w-xl text-base' => $service['wide'],
                            'text-[15px]' => !$service['wide'],
                        ])>
                            {{ $service['desc'] }}
                        </p>

                        {{-- Capability chips --}}
                        <div class="flex flex-wrap gap-1.5 mb-6">
                            @foreach ($service['tags'] as $tag)
                                <span
                                    class="inline-flex items-center text-[11px] font-medium text-slate-700
                                                           px-2.5 py-1 rounded-full
                                                           bg-white/80 border border-lavender-300/80
                                                           transition-all duration-300
                                                           group-hover:bg-primary-50 group-hover:border-primary-200/80 group-hover:text-primary-800">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>

                        {{-- Footer: CTA + arrow --}}
                        <div class="mt-auto flex items-center justify-between gap-3 pt-5 border-t border-lavender-300/60">
                            <span
                                class="text-sm font-medium text-slate-700 inline-flex items-center gap-2 transition-colors duration-300 group-hover:text-primary-700 whitespace-nowrap">
                                Learn more
                                @if ($service['wide'])
                                    <span aria-hidden="true" class="size-1 rounded-full bg-current opacity-40"></span>
                                    <span
                                        class="text-xs text-slate-500 group-hover:text-primary-500/80 font-mono uppercase tracking-[0.14em]">
                                        Strategy · Build · Scale
                                    </span>
                                @endif
                            </span>
                            <span class="grid place-items-center size-10 rounded-full bg-slate-900 text-white shrink-0
                                                 transition-all duration-500
                                                 group-hover:bg-gradient-to-br group-hover:from-primary-600 group-hover:to-blush-500
                                                 group-hover:-rotate-45 group-hover:scale-110
                                                 group-hover:shadow-[0_12px_28px_-6px_rgba(123,65,179,0.55)]">
                                <i data-lucide="arrow-up-right" class="size-4"></i>
                            </span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>

    {{-- ============ Marquee strip ============ --}}
    {{-- Edge-to-edge band that sits between cards and the next section --}}
    <div class="relative mt-14 lg:mt-20 py-7 border-y border-lavender-300/60 bg-white/40 backdrop-blur-sm overflow-hidden"
        aria-hidden="true">
        <div class="marquee-track gap-10 lg:gap-14 font-display font-bold text-2xl sm:text-3xl lg:text-4xl">
            {{-- Duplicate the word list twice so the -50% translate loops seamlessly --}}
            @for ($i = 0; $i < 2; $i++)
                @foreach ($marquee as $word)
                    <span class="whitespace-nowrap text-slate-900/15 transition-colors duration-300 hover:text-primary-600">
                        {{ $word }}
                    </span>
                    <span class="text-primary-500/40">&bull;</span>
                @endforeach
            @endfor
        </div>
    </div>
</section>