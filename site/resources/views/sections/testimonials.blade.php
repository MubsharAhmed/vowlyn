{{-- ============================================================================
TESTIMONIALS — "Trusted by Teams That Value Quality"
Editorial quote layout, oversized typographic mark
============================================================================ --}}
<section id="testimonials" class="section-vw">
    <div class="container-vw">
        <div class="grid lg:grid-cols-[1fr_1.2fr] gap-12 lg:gap-20 items-start">

            <div class="lg:sticky lg:top-32">
                <div class="eyebrow-row mb-5" data-reveal>
                    <span class="eyebrow">Testimonials</span>
                </div>
                <h2 class="headline-display text-4xl sm:text-5xl text-slate-900 mb-6" data-reveal
                    data-reveal-delay="0.05">
                    Trusted by Teams That <span class="text-brand-gradient">Value Quality.</span>
                </h2>
                <p class="text-on-surface/65 leading-relaxed max-w-md" data-reveal data-reveal-delay="0.12">
                    Our clients choose Vowlyn for high-impact product execution, reliable collaboration, and measurable
                    growth.
                </p>
            </div>

            <figure
                class="relative card-vw !p-10 lg:!p-14 bg-gradient-to-br from-lavender-50 via-white to-blush-50 border-primary-200"
                data-reveal data-reveal-delay="0.15">
                {{-- Oversized quote mark --}}
                <span aria-hidden="true"
                    class="absolute top-4 left-6 font-display text-[10rem] leading-none text-primary-700/15 select-none pointer-events-none">"</span>

                <blockquote
                    class="relative font-display text-2xl sm:text-3xl lg:text-4xl leading-[1.25] text-slate-900 font-medium tracking-tight text-pretty mb-10">
                    Working with Vowlyn felt like adding a senior product team overnight. Our store rebuild lifted
                    conversions 38% in the first quarter — and we finally own our stack.
                </blockquote>

                <figcaption class="relative flex items-center gap-4 pt-6 border-t border-lavender-300">
                    <div
                        class="size-14 rounded-full bg-gradient-to-br from-primary-300 to-blush-400 grid place-items-center text-white font-display font-bold text-xl shrink-0">
                        DA
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">Daniel Arian</p>
                        <p class="text-sm text-on-surface/55">Founder, Arian Rugs</p>
                    </div>
                    <div class="ml-auto flex gap-0.5">
                        @for ($s = 0; $s < 5; $s++)
                            <i data-lucide="star" class="size-4 text-blush-500 fill-blush-500"></i>
                        @endfor
                    </div>
                </figcaption>
            </figure>

        </div>
    </div>
</section>