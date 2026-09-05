{{-- ============================================================================
REEL — "See it in motion"
Exo-Ape-inspired full-bleed showreel section. The video plays as the section's
background; "Play" + "Projects" sit on top as fully visible display words.
============================================================================ --}}
<section id="reel"
    class="relative overflow-hidden bg-brand-stage text-white min-h-[80vh] lg:min-h-[640px] flex items-center">

    {{-- ============ Full-section background video ============ --}}
    {{-- TODO: replace src with the real Vowlyn showreel mp4 --}}
    <div aria-hidden="true" class="absolute inset-0 z-0">
        <video autoplay muted loop playsinline preload="metadata"
            poster="https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=2000&q=80"
            class="absolute inset-0 w-full h-full object-cover">
            <source src="https://videos.pexels.com/video-files/3209828/3209828-hd_1920_1080_25fps.mp4"
                type="video/mp4" />
        </video>

        {{-- dark scrim for legibility --}}
        <div class="absolute inset-0 bg-gradient-to-b from-black/65 via-black/35 to-black/70"></div>
        {{-- brand-purple radial tint for atmosphere --}}
        <div class="absolute inset-0 bg-[radial-gradient(at_50%_50%,rgba(75,0,130,0.28)_0%,transparent_65%)]">
        </div>
    </div>

    {{-- ============ Foreground content ============ --}}
    <div class="relative z-10 section-vw w-full">
        <div class="container-vw text-center">

            {{-- top eyebrow --}}
            <div class="mb-10 lg:mb-14" data-reveal>
                <span class="inline-flex items-center gap-2 eyebrow !text-white/70">
                    <span aria-hidden="true">+</span> See it in motion
                </span>
            </div>

            {{-- ============ Big title: PLAY PROJECTS ============ --}}
            <div data-reel class="flex flex-col lg:flex-row items-center justify-center gap-4 lg:gap-12 xl:gap-20">

                {{-- PLAY word (slides in from left on scroll) --}}
                <span data-reel-word="left"
                    class="display-reel text-white leading-[0.85] select-none whitespace-nowrap">
                    Play
                </span>

                {{-- PROJECTS word (slides in from right on scroll) --}}
                <span data-reel-word="right"
                    class="display-reel text-white leading-[0.85] select-none whitespace-nowrap">
                    Projects
                </span>

                {{-- hidden anchor for GSAP target so initReelReveal() finds something --}}
                <span data-reel-media aria-hidden="true" class="sr-only"></span>
            </div>

            {{-- showreel duration chip --}}
            <div class="mt-10 lg:mt-14 flex justify-center" data-reveal data-reveal-delay="0.2">
                <span
                    class="inline-flex items-center gap-2 text-[11px] font-mono uppercase tracking-[0.16em] px-3 py-1.5 rounded-full bg-white/8 text-white/80 border border-white/15 backdrop-blur-md">
                    <span class="size-1.5 rounded-full bg-blush-400 animate-pulse"></span>
                    Showreel · 01 : 24
                </span>
            </div>
        </div>
    </div>
</section>