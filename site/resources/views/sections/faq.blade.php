{{-- ============================================================================
    FAQ — "Answers founders ask us" (People Also Ask — AEO)
    Visible companion to the FAQPage JSON-LD in pages/home @push('head').
    Sits between the dark Stack section and the light Contact section.
============================================================================ --}}
@php
    $homeFaqs = [
        [
            'q' => 'What does Vowlyn do?',
            'a' => 'Vowlyn is a custom software development studio. We design, engineer, and scale web apps, mobile apps, AI features, and SaaS platforms for founders and teams across North America, Europe, and MENA.',
        ],
        [
            'q' => 'Is Vowlyn an agency or a software development studio?',
            'a' => 'A software development studio — not a traditional agency. We are a small, senior-only team that owns strategy, design, engineering, and launch in-house. No outsourced contractors, no middle-management layer.',
        ],
        [
            'q' => 'What software development services does Vowlyn offer?',
            'a' => 'Six disciplines: modern web app development, mobile app development, AI integration, scalable SaaS development, enterprise security, and cloud DevOps — available individually or as one end-to-end build.',
        ],
        [
            'q' => 'How fast can Vowlyn start a project?',
            'a' => 'Discovery sprints typically begin within a week, and we reply to every project request within 24 hours.',
        ],
        [
            'q' => 'Who owns the code and cloud accounts?',
            'a' => 'You do. Everything ships to your own repositories and cloud accounts from day one, with no vendor lock-in.',
        ],
    ];
@endphp

<section id="faq" aria-labelledby="paa-home" class="section-vw bg-gradient-to-br from-[#4a1a6b] via-[#3b1560] to-[#2d1045] relative overflow-hidden">
    {{-- Atmosphere — soft bridge from the dark Stack stage back to light --}}
    <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 -left-24 size-[26rem] rounded-full bg-primary-300/20 blur-3xl"></div>
        <div class="absolute bottom-0 -right-24 size-[24rem] rounded-full bg-blush-200/30 blur-3xl"></div>
    </div>

    <div class="container-vw relative">
        <div class="grid lg:grid-cols-[0.8fr_1.2fr] gap-10 lg:gap-16 items-start">
            <div data-reveal>
                <span class="eyebrow !text-primary-300">People also ask</span>
                <h2 id="paa-home" class="headline-display text-3xl sm:text-4xl lg:text-5xl mt-3 text-white">
                    Straight answers, <span class="text-white">before you even ask.</span>
                </h2>
                <p class="text-white/70 mt-4 leading-relaxed max-w-md">
                    The questions every founder asks a software development studio — answered plainly. Anything else? A
                    30-minute call covers it.
                </p>
                <a href="https://calendly.com/junaidswati/new-meeting" target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-6">
                    Ask us directly
                    <i data-lucide="arrow-up-right" class="size-4"></i>
                </a>
            </div>

            <x-faq-accordion :dark="true" :items="$homeFaqs" />
        </div>
    </div>
</section>
