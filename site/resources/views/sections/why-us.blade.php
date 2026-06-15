{{-- ============================================================================
WHY US — "Built for Teams That Need Premium Outcomes"
4 differentiators on a soft lavender/cream stage
============================================================================ --}}
@php
    $reasons = [
        ['Senior Product Thinking', 'Every decision aligns with conversion, retention, and long-term product equity.', 'brain-circuit'],
        ['Design + Engineering Unity', 'A collaborative pipeline that removes friction between visuals and implementation.', 'workflow'],
        ['Fast, Predictable Delivery', 'Milestone-based execution with transparent progress and dependable velocity.', 'timer-reset'],
        ['Growth-Ready Architecture', 'Platforms designed to handle scale, new features, and enterprise expectations.', 'network'],
    ];
@endphp

<section id="why-us" class="section-vw bg-gradient-to-b from-surface to-lavender-100/50 relative overflow-hidden">
    {{-- Decorative SVG mesh --}}
    <div aria-hidden="true"
        class="absolute top-0 right-0 size-[40rem] rounded-full bg-primary-300/15 blur-3xl pointer-events-none"></div>

    <div class="container-vw relative">
        <div class="grid lg:grid-cols-[1fr_1.2fr] gap-10 mb-14 lg:mb-20 items-end">
            <div>
                <div class="eyebrow-row mb-5" data-reveal>
                    <span class="eyebrow">05 / Why Vowlyn</span>
                </div>
                <h2 class="headline-display text-4xl sm:text-5xl lg:text-6xl text-slate-900" data-reveal
                    data-reveal-delay="0.05">
                    Built for Teams That<br /><span class="text-brand-gradient">Need Premium Outcomes.</span>
                </h2>
            </div>
            <p class="text-on-surface/65 text-lg leading-relaxed max-w-lg lg:justify-self-end" data-reveal
                data-reveal-delay="0.12">
                We blend strategic product insight with precision engineering so your digital platform becomes a
                long-term competitive advantage.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-5" data-stagger="0.1">
            @foreach ($reasons as $i => [$title, $desc, $icon])
                <article data-stagger-item
                    class="relative group rounded-3xl p-8 lg:p-10 bg-white border border-lavender-300 overflow-hidden hover:border-primary-300 transition-colors duration-500">
                    {{-- Number watermark --}}
                    <span aria-hidden="true"
                        class="absolute -top-4 -right-2 font-display text-[10rem] font-bold leading-none text-lavender-200/60 select-none pointer-events-none">
                        {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                    </span>

                    <div class="relative flex items-start gap-6">
                        <div
                            class="grid place-items-center size-16 rounded-2xl bg-gradient-to-br from-primary-100 to-lavender-300 text-primary-700 shrink-0 group-hover:scale-110 transition-transform duration-500">
                            <i data-lucide="{{ $icon }}" class="size-8"></i>
                        </div>
                        <div>
                            <h3 class="font-display text-2xl font-semibold text-slate-900 mb-3">{{ $title }}</h3>
                            <p class="text-on-surface/65 leading-relaxed">{{ $desc }}</p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>