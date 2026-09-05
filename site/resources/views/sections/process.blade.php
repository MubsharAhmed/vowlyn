{{-- ============================================================================
PROCESS — "A Structured Workflow That Delivers Fast"
Editorial phase deck: rich glass cards with huge background numerals, gradient
icon halos, deliverable chips, and a scroll-tracked progress thread.
============================================================================ --}}
@php
    $steps = [
        [
            'num' => '01',
            'phase' => 'Phase one',
            'title' => 'Strategy & Discovery',
            'desc' => 'We define business outcomes, map user journeys, and shape a technical blueprint before a single pixel ships.',
            'icon' => 'compass',
            'deliverables' => ['Discovery workshops', 'User research', 'Tech blueprint'],
            'duration' => '1 – 2 weeks',
        ],
        [
            'num' => '02',
            'phase' => 'Phase two',
            'title' => 'UX Systems & Prototyping',
            'desc' => 'Interactive flows and a scalable design system validate the experience before the build starts.',
            'icon' => 'pencil-ruler',
            'deliverables' => ['Wireframes', 'Design system', 'Clickable prototype'],
            'duration' => '2 – 4 weeks',
        ],
        [
            'num' => '03',
            'phase' => 'Phase three',
            'title' => 'Engineering & Integration',
            'desc' => 'Senior engineers ship scalable web, mobile, and AI layers with performance, accessibility, and tests built in.',
            'icon' => 'cpu',
            'deliverables' => ['Production build', 'APIs & integrations', 'QA & CI'],
            'duration' => '4 – 12 weeks',
        ],
        [
            'num' => '04',
            'phase' => 'Phase four',
            'title' => 'Launch & Growth',
            'desc' => 'Post-launch iteration, experimentation, and analytics tuning turn the launch into compounding growth.',
            'icon' => 'rocket',
            'deliverables' => ['Launch plan', 'Analytics setup', 'A / B testing'],
            'duration' => 'Ongoing',
        ],
    ];
@endphp

<section id="process" class="relative bg-brand-stage text-white grain overflow-hidden">
    {{-- Bottom hairline separator (bg-brand-stage already supplies the aurora) --}}
    <div aria-hidden="true"
        class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-white/15 to-transparent pointer-events-none">
    </div>

    <div class="container-vw relative section-vw">
        {{-- ============ Header ============ --}}
        <div class="grid lg:grid-cols-[1fr_1.1fr] gap-10 mb-16 lg:mb-24 items-end">
            <div>
                <div class="eyebrow-row mb-5" data-reveal>
                    <span class="eyebrow !text-primary-300">Process</span>
                </div>
                <h2 class="headline-display text-4xl sm:text-5xl lg:text-6xl text-white" data-reveal
                    data-reveal-delay="0.05">
                    A Software Development Process<br />Built to <span class="text-brand-gradient">Deliver Fast.</span>
                </h2>
            </div>

            <div class="space-y-6 max-w-lg lg:justify-self-end" data-reveal data-reveal-delay="0.12">
                <p class="text-white/65 text-lg leading-relaxed">
                    Every engagement moves through four tight phases — strategy, prototyping, engineering, and
                    launch — so you always know what happens next, and when.
                </p>
                {{-- Mini stats row --}}
                <div class="flex flex-wrap items-center gap-x-8 gap-y-3 pt-2">
                    <div>
                        <div class="font-display text-2xl font-bold text-white">4</div>
                        <div class="text-[11px] font-mono uppercase tracking-[0.16em] text-white/45">
                            Clear phases
                        </div>
                    </div>
                    <div class="h-8 w-px bg-white/10"></div>
                    <div>
                        <div class="font-display text-2xl font-bold text-white">8&ndash;16</div>
                        <div class="text-[11px] font-mono uppercase tracking-[0.16em] text-white/45">
                            Avg. weeks
                        </div>
                    </div>
                    <div class="h-8 w-px bg-white/10"></div>
                    <div>
                        <div class="font-display text-2xl font-bold text-white">100<span
                                class="text-primary-400">%</span></div>
                        <div class="text-[11px] font-mono uppercase tracking-[0.16em] text-white/45">
                            On-track delivery
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ Scroll-tracked progress thread (desktop only) ============ --}}
        <div class="hidden lg:block mb-10" aria-hidden="true">
            <div class="relative h-px rounded-full bg-white/10 overflow-hidden">
                <div data-process-progress
                    class="absolute inset-y-0 left-0 w-0 bg-gradient-to-r from-primary-500 via-blush-400 to-primary-300 rounded-full transition-[width] duration-150 ease-out">
                </div>
            </div>
            <div class="mt-3 grid grid-cols-4 gap-5 lg:gap-6">
                @foreach ($steps as $s)
                    <div class="text-[10px] font-mono uppercase tracking-[0.18em] text-white/35">
                        {{ $s['num'] }} / {{ $s['phase'] }}
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ============ Phase cards ============ --}}
        <div class="relative grid md:grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-6" data-stagger="0.1" data-process-grid>
            @foreach ($steps as $step)
                <article data-stagger-item class="group relative h-full">
                    {{-- Outer wrapper with gradient border --}}
                    <div
                        class="relative h-full rounded-2xl p-[1px] transition-all duration-500
                                        bg-gradient-to-b from-white/20 via-white/8 to-white/5
                                        group-hover:from-primary-300/80 group-hover:via-blush-300/40 group-hover:to-primary-300/15">

                        {{-- Frosted glass card — translucent so the brand-stage hues bleed through --}}
                        <div class="relative h-full rounded-2xl overflow-hidden p-6 lg:p-7
                                            bg-[#160828]/70 backdrop-blur-2xl backdrop-saturate-150
                                            transition-all duration-500
                                            group-hover:bg-[#1d0a35]/75
                                            group-hover:-translate-y-2
                                            group-hover:shadow-[0_30px_80px_-20px_rgba(123,65,179,0.55)]">

                            {{-- Inner highlight gradient — gives the card a subtle vertical lift --}}
                            <div aria-hidden="true"
                                class="absolute inset-0 bg-gradient-to-b from-white/[0.06] via-transparent to-transparent pointer-events-none">
                            </div>

                            {{-- Huge background numeral --}}
                            <div aria-hidden="true" class="absolute -top-6 -right-3 font-display font-black leading-none select-none pointer-events-none
                                               text-[9rem] lg:text-[10rem]
                                               bg-gradient-to-br from-white/[0.07] to-white/[0.01] bg-clip-text text-transparent
                                               transition-all duration-500
                                               group-hover:from-primary-400/40 group-hover:to-blush-500/15">
                                {{ $step['num'] }}
                            </div>

                            {{-- Top row: icon halo + phase tag --}}
                            <div class="relative flex items-start justify-between mb-7">
                                <div
                                    class="relative size-14 rounded-xl grid place-items-center shrink-0
                                                    bg-gradient-to-br from-primary-500/25 to-blush-500/10
                                                    border border-white/15
                                                    shadow-[inset_0_1px_0_rgba(255,255,255,0.18),0_10px_30px_-10px_rgba(75,0,130,0.45)]
                                                    transition-all duration-500
                                                    group-hover:scale-110 group-hover:rotate-[6deg]
                                                    group-hover:shadow-[inset_0_1px_0_rgba(255,255,255,0.25),0_18px_45px_-10px_rgba(180,90,255,0.55)]">
                                    <i data-lucide="{{ $step['icon'] }}" class="size-6 text-white"></i>
                                    {{-- Halo glow --}}
                                    <div aria-hidden="true"
                                        class="absolute inset-0 rounded-xl bg-primary-400/0 group-hover:bg-primary-400/20 blur-md transition-all duration-500">
                                    </div>
                                </div>

                                <span class="text-[10px] font-mono uppercase tracking-[0.2em] text-primary-300/85 mt-2">
                                    {{ $step['phase'] }}
                                </span>
                            </div>

                            {{-- Title + description --}}
                            <h3 class="relative font-display text-xl lg:text-[1.4rem] font-semibold leading-tight mb-3">
                                {{ $step['title'] }}
                            </h3>
                            <p class="relative text-white/55 leading-relaxed text-sm mb-6">
                                {{ $step['desc'] }}
                            </p>

                            {{-- Deliverables --}}
                            <div class="relative mb-6">
                                <div class="text-[10px] font-mono uppercase tracking-[0.2em] text-white/40 mb-2.5">
                                    Deliverables
                                </div>
                                <ul class="space-y-1.5">
                                    @foreach ($step['deliverables'] as $d)
                                        <li class="flex items-center gap-2 text-[13px] text-white/75">
                                            <span aria-hidden="true"
                                                class="size-1 rounded-full bg-gradient-to-r from-primary-400 to-blush-400"></span>
                                            {{ $d }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Bottom: duration + arrow --}}
                            <div class="relative flex items-center justify-between pt-5 border-t border-white/10">
                                <span
                                    class="inline-flex items-center gap-1.5 text-[11px] font-mono uppercase tracking-[0.15em] text-white/55">
                                    <i data-lucide="clock-3" class="size-3"></i>
                                    {{ $step['duration'] }}
                                </span>
                                <span class="size-8 rounded-full grid place-items-center bg-white/5 border border-white/10
                                                   transition-all duration-500
                                                   group-hover:bg-gradient-to-br group-hover:from-primary-500 group-hover:to-blush-500
                                                   group-hover:border-transparent
                                                   group-hover:-rotate-45 group-hover:scale-110">
                                    <i data-lucide="arrow-right" class="size-3.5 text-white"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- ============ Footer CTA ============ --}}
        <div class="mt-14 lg:mt-20 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6"
            data-reveal>
            <p class="text-white/60 text-sm sm:text-base max-w-md">
                Want a walkthrough of how this works for your project?
            </p>
            <a href="#contact"
                class="group inline-flex items-center gap-2 text-sm font-medium text-white px-5 py-3 rounded-full border border-white/15 hover:border-primary-300/60 hover:bg-white/5 transition-all">
                Start a conversation
                <i data-lucide="arrow-up-right"
                    class="size-4 transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5"></i>
            </a>
        </div>
    </div>
</section>