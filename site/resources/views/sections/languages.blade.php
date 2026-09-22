{{-- ============================================================================
LANGUAGES — "Engineering Loadout"
Studio tool plates + stats strip + streaming type marquees on brand stage
============================================================================ --}}
@php
    /* ── Tool plates ─ each tool gets a unique geometric stroke-glyph drawn in via GSAP ── */
    $signature = [
        [
            'name' => 'React',
            'category' => 'Frontend Architecture',
            'domain' => 'Interface systems',
            'tenure' => '8 yrs',
            'reliability' => '98%',
            'since' => '2018',
            'stops' => ['#18d2ff', '#5c7cf5'],
            // Orbital atom — three rotating ellipses + nucleus
            'glyph' => '<ellipse cx="24" cy="24" rx="22" ry="9"/>
                            <ellipse cx="24" cy="24" rx="22" ry="9" transform="rotate(60 24 24)"/>
                            <ellipse cx="24" cy="24" rx="22" ry="9" transform="rotate(120 24 24)"/>
                            <circle cx="24" cy="24" r="2.5" fill="currentColor" stroke="none"/>',
        ],
        [
            'name' => 'Next.js',
            'category' => 'Edge Framework',
            'domain' => 'Hybrid rendering',
            'tenure' => '6 yrs',
            'reliability' => '96%',
            'since' => '2020',
            'stops' => ['#5c7cf5', '#7b41b3'],
            // Forward chevrons + trailing rays
            'glyph' => '<path d="M8 10 L22 24 L8 38"/>
                            <path d="M22 10 L36 24 L22 38"/>
                            <line x1="30" y1="24" x2="44" y2="24"/>
                            <circle cx="44" cy="24" r="1.5" fill="currentColor" stroke="none"/>',
        ],
        [
            'name' => 'Laravel',
            'category' => 'Server Framework',
            'domain' => 'API & admin backends',
            'tenure' => '9 yrs',
            'reliability' => '99%',
            'since' => '2016',
            'stops' => ['#7b41b3', '#c8459b'],
            // Layered diamonds — sediment
            'glyph' => '<path d="M24 4 L42 18 L24 32 L6 18 Z"/>
                            <path d="M24 16 L42 30 L24 44 L6 30 Z"/>',
        ],
        [
            'name' => 'Python',
            'category' => 'AI · Data Pipelines',
            'domain' => 'ML inference & ETL',
            'tenure' => '7 yrs',
            'reliability' => '95%',
            'since' => '2019',
            'stops' => ['#c8459b', '#ff7ab8'],
            // Interlocking rounded squares
            'glyph' => '<rect x="5" y="5" width="22" height="22" rx="5"/>
                            <rect x="21" y="21" width="22" height="22" rx="5"/>',
        ],
        [
            'name' => 'AWS',
            'category' => 'Cloud Infrastructure',
            'domain' => 'Production observability',
            'tenure' => '6 yrs',
            'reliability' => '99.9%',
            'since' => '2019',
            'stops' => ['#ff7ab8', '#ef4444'],
            // Stepped pyramid — geological strata
            'glyph' => '<path d="M4 42 L24 6 L44 42 Z"/>
                            <path d="M12 42 L24 20 L36 42"/>
                            <line x1="4" y1="42" x2="44" y2="42"/>',
        ],
        [
            'name' => 'OpenAI',
            'category' => 'LLM Orchestration',
            'domain' => 'Agentic workflows',
            'tenure' => '2 yrs',
            'reliability' => '92%',
            'since' => '2023',
            'stops' => ['#18d2ff', '#c8459b'],
            // Hub-and-spokes node graph
            'glyph' => '<circle cx="24" cy="24" r="18"/>
                            <circle cx="24" cy="24" r="8"/>
                            <line x1="24" y1="6" x2="24" y2="14"/>
                            <line x1="24" y1="34" x2="24" y2="42"/>
                            <line x1="6" y1="24" x2="14" y2="24"/>
                            <line x1="34" y1="24" x2="42" y2="24"/>',
        ],
    ];

    $stats = [
        ['v' => '20+', 'l' => 'Languages'],
        ['v' => '35+', 'l' => 'Frameworks'],
        ['v' => '12+', 'l' => 'Cloud · Infra'],
        ['v' => '100%', 'l' => 'Production grade'],
    ];

    $marquees = [
        ['tag' => 'LANG', 'items' => ['JavaScript', 'TypeScript', 'Python', 'Go', 'Rust', 'PHP', 'Ruby', 'Java', 'Kotlin', 'Swift', 'Dart', 'C#', 'SQL', 'Bash', 'HTML', 'CSS'], 'speed' => 'medium', 'reverse' => false],
        ['tag' => 'FW', 'items' => ['React', 'Next.js', 'Vue', 'Nuxt', 'Angular', 'Svelte', 'Astro', 'Remix', 'Laravel', 'Django', 'FastAPI', 'NestJS', 'Express', 'Spring Boot', 'Rails', '.NET', 'Flutter', 'React Native'], 'speed' => 'slow', 'reverse' => true],
        ['tag' => 'OPS', 'items' => ['AWS', 'GCP', 'Cloudflare', 'Docker', 'Kubernetes', 'Terraform', 'Vercel', 'Supabase', 'Postgres', 'MongoDB', 'Redis', 'OpenAI', 'Anthropic', 'Pinecone', 'Stripe', 'Twilio'], 'speed' => 'medium', 'reverse' => false],
    ];
@endphp

<section id="languages" class="relative bg-brand-stage text-white overflow-hidden grain">
    <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
        <div class="absolute top-1/4 -left-32 size-[28rem] rounded-full bg-primary-700/25 blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 size-[28rem] rounded-full bg-blush-500/15 blur-3xl"></div>
    </div>

    <div class="container-vw relative section-vw">

        {{-- ═══ Header ═══ --}}
        <div class="grid lg:grid-cols-[1fr_1.2fr] gap-10 mb-14 lg:mb-20 items-end">
            <div>
                <div class="eyebrow-row mb-5" data-reveal>
                    <span class="eyebrow !text-primary-300">Stack</span>
                </div>
                <h2 class="headline-display text-4xl sm:text-5xl lg:text-6xl text-white" data-reveal
                    data-reveal-delay="0.05">
                    The Modern Stack Behind<br /><span class="text-brand-gradient">Every Build.</span>
                </h2>
            </div>
            <div class="space-y-5 lg:justify-self-end max-w-lg" data-reveal data-reveal-delay="0.12">
                <p class="text-white/65 text-lg leading-relaxed">
                    Next.js, React Native, Laravel, Python, AWS, and OpenAI — chosen for performance, scalability,
                    and long-term maintainability, and gone deep where it matters.
                </p>
                <div
                    class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 bg-white/[0.04] border border-white/10">
                    <span class="relative grid place-items-center size-2">
                        <span class="absolute inset-0 rounded-full bg-emerald-400 animate-ping opacity-60"></span>
                        <span class="relative size-2 rounded-full bg-emerald-400"></span>
                    </span>
                    <span class="text-[10px] font-mono uppercase tracking-[0.18em] text-white/70">Battle-tested in
                        production</span>
                </div>
            </div>
        </div>

        {{-- ═══ Signature tool plates ═══ --}}
        <!-- <div class="mb-16 lg:mb-20">
            <div class="flex items-center justify-between mb-8 flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <p class="eyebrow !text-white/65">Signature Loadout</p>
                </div>
                <p class="text-[10px] font-mono uppercase tracking-[0.18em] text-white/35">
                    6 of 67 · the tools we reach for first
                </p>
            </div>

            {{-- Perspective wrapper enables 3D hover tilt on cards --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-5 [perspective:1400px]">
                @foreach ($signature as $i => $t)
                    @php $gid = 'plate-' . $i; @endphp
                    <article data-loadout-card data-stagger-item
                        class="loadout-plate group relative rounded-2xl overflow-hidden bg-[#0d0420]/55 backdrop-blur-xl border border-white/[0.08]"
                        style="transform-style: preserve-3d;">

                        {{-- Etched grid background --}}
                        <div aria-hidden="true"
                            class="absolute inset-0 plate-grid opacity-[0.12] group-hover:opacity-[0.22] transition-opacity duration-700">
                        </div>

                        {{-- Corner registration marks (blueprint precision) --}}
                        <span aria-hidden="true"
                            class="absolute top-3 left-3 size-2.5 border-t border-l border-white/30"></span>
                        <span aria-hidden="true"
                            class="absolute top-3 right-3 size-2.5 border-t border-r border-white/30"></span>
                        <span aria-hidden="true"
                            class="absolute bottom-3 left-3 size-2.5 border-b border-l border-white/30"></span>
                        <span aria-hidden="true"
                            class="absolute bottom-3 right-3 size-2.5 border-b border-r border-white/30"></span>

                        {{-- Inner content (slightly inset from registration marks) --}}
                        <div class="relative p-6 lg:p-7 min-h-[19rem] flex flex-col" style="transform: translateZ(20px);">

                            {{-- Top row: volume tag + glyph --}}
                            <div class="flex items-start justify-between mb-auto">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-mono uppercase tracking-[0.2em] text-white/40">
                                        VOL.{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>

                                {{-- Geometric stroke-art glyph (unique per tool, draws in via GSAP) --}}
                                <svg viewBox="0 0 48 48"
                                    class="size-14 lg:size-16 -mr-1 -mt-1 transition-transform duration-700 group-hover:rotate-[8deg] group-hover:scale-110"
                                    fill="none" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"
                                    style="color: {{ $t['stops'][1] }};" aria-hidden="true">
                                    <defs>
                                        <linearGradient id="{{ $gid }}-stroke" x1="0" y1="0" x2="1" y2="1">
                                            <stop offset="0%" stop-color="{{ $t['stops'][0] }}" />
                                            <stop offset="100%" stop-color="{{ $t['stops'][1] }}" />
                                        </linearGradient>
                                    </defs>
                                    <g stroke="url(#{{ $gid }}-stroke)" data-glyph-group>
                                        {!! $t['glyph'] !!}
                                    </g>
                                </svg>
                            </div>

                            {{-- Mid: category --}}
                            <p class="text-[10px] font-mono uppercase tracking-[0.18em] text-white/45 mt-7 mb-2">
                                {{ $t['category'] }}
                            </p>

                            {{-- Big bleed name --}}
                            <h3 class="font-display font-bold text-white leading-[0.95] tracking-tight text-[2.5rem] sm:text-5xl lg:text-[3rem] xl:text-[3.25rem] mb-5"
                                style="background: linear-gradient(135deg, #ffffff 0%, #ffffff 55%, {{ $t['stops'][1] }} 100%); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">
                                {{ $t['name'] }}
                            </h3>

                            {{-- Spec table — engineering metadata --}}
                            <dl
                                class="mt-auto pt-4 border-t border-dashed border-white/10 space-y-1.5 text-[11px] font-mono">
                                <div class="flex items-baseline justify-between gap-3">
                                    <dt class="uppercase tracking-[0.16em] text-white/35">Domain</dt>
                                    <span aria-hidden="true"
                                        class="flex-1 border-b border-dotted border-white/10 mb-0.5"></span>
                                    <dd class="text-white/80 truncate">{{ $t['domain'] }}</dd>
                                </div>
                                <div class="flex items-baseline justify-between gap-3">
                                    <dt class="uppercase tracking-[0.16em] text-white/35">Tenure</dt>
                                    <span aria-hidden="true"
                                        class="flex-1 border-b border-dotted border-white/10 mb-0.5"></span>
                                    <dd class="text-white/80 tabular-nums">{{ $t['tenure'] }}</dd>
                                </div>
                                <div class="flex items-baseline justify-between gap-3">
                                    <dt class="uppercase tracking-[0.16em] text-white/35">Reliability</dt>
                                    <span aria-hidden="true"
                                        class="flex-1 border-b border-dotted border-white/10 mb-0.5"></span>
                                    <dd class="text-white/80 tabular-nums">{{ $t['reliability'] }}</dd>
                                </div>
                                <div class="flex items-baseline justify-between gap-3">
                                    <dt class="uppercase tracking-[0.16em] text-white/35">Since</dt>
                                    <span aria-hidden="true"
                                        class="flex-1 border-b border-dotted border-white/10 mb-0.5"></span>
                                    <dd class="text-white/80 tabular-nums">{{ $t['since'] }}</dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Bottom accent bar — expands on hover --}}
                        <span aria-hidden="true"
                            class="absolute inset-x-0 bottom-0 h-[2px] origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-700 ease-out"
                            style="background: linear-gradient(90deg, {{ $t['stops'][0] }}, {{ $t['stops'][1] }});"></span>
                    </article>
                @endforeach
            </div>
        </div> -->

        {{-- ═══ Stats strip ═══ --}}
        <!-- <div class="mb-16 lg:mb-20 grid grid-cols-2 lg:grid-cols-4 gap-px overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04]"
            data-reveal data-reveal-delay="0.05">
            @foreach ($stats as $s)
                <div class="bg-[#0d0420]/60 backdrop-blur-xl px-5 py-6 lg:px-6 lg:py-7 flex flex-col">
                    <span
                        class="font-display text-4xl lg:text-5xl font-bold text-white tabular-nums leading-none">{{ $s['v'] }}</span>
                    <span class="mt-2 text-[10px] font-mono uppercase tracking-[0.2em] text-white/45">{{ $s['l'] }}</span>
                </div>
            @endforeach
        </div> -->

        {{-- ═══ Streaming type marquees — full bleed ═══ --}}
        <div class="space-y-3 lg:space-y-4">
            @foreach ($marquees as $row)
                <div class="flex items-center gap-4 lg:gap-6" data-reveal>
                    {{-- Side tag --}}
                    <span
                        class="shrink-0 hidden sm:inline-flex items-center justify-center text-[10px] font-mono uppercase tracking-[0.22em] text-white/45 w-12 py-1 rounded-md border border-white/10 bg-white/[0.04]">
                        {{ $row['tag'] }}
                    </span>

                    {{-- Marquee lane --}}
                    <div class="marquee-lane flex-1 min-w-0 py-3 lg:py-4">
                        <div class="marquee-track gap-10 lg:gap-14 px-4" data-marquee-speed="{{ $row['speed'] }}"
                            @if($row['reverse']) data-marquee-reverse @endif>
                            @for ($pass = 0; $pass < 2; $pass++)
                                @foreach ($row['items'] as $item)
                                    <span class="inline-flex items-center gap-10 lg:gap-14 shrink-0">
                                        <span
                                            class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-white/70 hover:text-white transition-colors whitespace-nowrap"
                                            style="font-feature-settings: 'ss01' on;">{{ $item }}</span>
                                        <span aria-hidden="true" class="text-white/15">◆</span>
                                    </span>
                                @endforeach
                            @endfor
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
