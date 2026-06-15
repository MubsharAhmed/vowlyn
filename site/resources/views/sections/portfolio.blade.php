{{-- ============================================================================
PORTFOLIO — "Real Projects, Measurable Outcomes"
Exo-Ape-inspired asymmetric image collage. Cards 2 & 4 parallax slowly while
cards 1 & 3 are anchored, giving the illusion of depth on scroll.
============================================================================ --}}
@php
    // Stock placeholder hero shots — swap with real project shots later.
    // (Stable Unsplash IDs used elsewhere on the site.)
    $projects = [
        [
            'category' => 'Service Website',
            'num' => '01',
            'name' => 'Burloak Painting',
            'desc' => 'Modern site for commercial & residential painting with strong local-lead generation.',
            'tags' => ['Service Platform', 'Local SEO', 'Lead Conversion'],
            'url' => 'https://burlingtonspainters.com/',
            'image' => 'https://images.unsplash.com/photo-1562259949-e8e7689d7828?auto=format&fit=crop&w=1400&q=80',
            'alt' => 'Painter rolling fresh paint on an interior wall',
            // Layout
            'span' => 'lg:col-span-7',
            'aspect' => 'aspect-[5/6]', // tall
            'parallax' => false,
            'tone' => 'lavender',
        ],
        [
            'category' => 'E-Commerce Platform',
            'num' => '02',
            'name' => 'Arian Rugs',
            'desc' => 'Premium rug commerce with refined browsing and checkout.',
            'tags' => ['E-commerce', 'Catalog UX', 'Conversion Design'],
            'url' => 'https://arianrugs.com/',
            'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
            'alt' => 'Persian rug detail with warm tones',
            'span' => 'lg:col-span-5',
            'aspect' => 'aspect-[4/3] lg:aspect-[4/5]', // shorter — floater
            'parallax' => '0.12',
            'tone' => 'blush',
        ],
        [
            'category' => 'Operations Platform',
            'num' => '03',
            'name' => 'Ontario Buying Group',
            'desc' => 'Multi-store purchasing & dispatch system for operational scale.',
            'tags' => ['B2B SaaS', 'Dispatch Workflows', 'Multi-store Ops'],
            'url' => 'https://ontariobuyinggroup.com/',
            'image' => 'https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?auto=format&fit=crop&w=1200&q=80',
            'alt' => 'Operations dashboard on a laptop',
            'span' => 'lg:col-span-5',
            'aspect' => 'aspect-[4/3] lg:aspect-[4/5]',
            'parallax' => false,
            'tone' => 'ink',
        ],
        [
            'category' => 'Single Product E-Commerce',
            'num' => '04',
            'name' => 'Lumea',
            'desc' => 'Conversion-focused single-product store with a streamlined buying journey.',
            'tags' => ['Single Product Store', 'E-commerce', 'Conversion Funnel'],
            'url' => 'https://lumea.pk/',
            'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=1400&q=80',
            'alt' => 'Minimal product photography on neutral background',
            'span' => 'lg:col-span-7',
            'aspect' => 'aspect-[5/6]',
            'parallax' => '0.10',
            'tone' => 'lavender',
        ],
    ];
@endphp

<section id="projects" class="section-vw">
    <div class="container-vw">

        {{-- ============ Header ============ --}}
        <div class="grid lg:grid-cols-[1fr_1fr] gap-10 mb-14 lg:mb-20 items-end">
            <div>
                <div class="eyebrow-row mb-5" data-reveal>
                    <span class="eyebrow">04 / Portfolio</span>
                </div>
                <h2 class="headline-display text-4xl sm:text-5xl lg:text-6xl text-slate-900" data-reveal
                    data-reveal-delay="0.05">
                    Real Projects,<br /><span class="text-brand-gradient">Measurable Outcomes.</span>
                </h2>
            </div>
            <p class="text-on-surface/65 text-lg leading-relaxed max-w-md lg:justify-self-end" data-reveal
                data-reveal-delay="0.12">
                A snapshot of the digital platforms we have designed and engineered for businesses across services,
                commerce, and operations.
            </p>
        </div>

        {{-- ============ Asymmetric image collage (Exo Ape pattern) ============ --}}
        {{-- data-parallax-group establishes scroll bounds for child parallax items --}}
        <div data-parallax-group data-stagger="0.12"
             class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-7 items-start">

            @foreach ($projects as $i => $p)
                <a href="{{ $p['url'] }}" target="_blank" rel="noopener noreferrer"
                   data-stagger-item
                   @if ($p['parallax']) data-parallax="{{ $p['parallax'] }}" @endif
                   class="project-card group relative {{ $p['span'] }} block rounded-3xl overflow-hidden
                          {{ $i === 1 ? 'lg:mt-16' : '' }} {{ $i === 3 ? 'lg:-mt-12' : '' }}">

                    {{-- Image well --}}
                    <div class="relative w-full {{ $p['aspect'] }} overflow-hidden rounded-3xl
                                bg-gradient-to-br from-lavender-200 via-white to-lavender-300">
                        <img src="{{ $p['image'] }}" alt="{{ $p['alt'] }}"
                             loading="lazy" width="1200" height="1500"
                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-[1200ms] ease-out group-hover:scale-[1.04]" />

                        {{-- gradient scrim for text legibility --}}
                        <div aria-hidden="true"
                             class="absolute inset-0 bg-gradient-to-t
                                    {{ $p['tone'] === 'ink' ? 'from-black/80 via-black/20 to-transparent' : 'from-slate-900/80 via-slate-900/20 to-transparent' }}">
                        </div>

                        {{-- top-left eyebrow chip --}}
                        <div class="absolute top-5 left-5 right-5 flex items-start justify-between gap-3">
                            <span class="eyebrow !text-white/85 bg-black/30 backdrop-blur-md rounded-full px-3 py-1.5 border border-white/10">
                                {{ $p['num'] }} / {{ $p['category'] }}
                            </span>
                            <span aria-hidden="true"
                                  class="grid place-items-center size-11 rounded-full bg-white/12 text-white backdrop-blur-md border border-white/20 transition-all duration-500 group-hover:rotate-45 group-hover:scale-110 group-hover:bg-white group-hover:text-primary-800">
                                <i data-lucide="arrow-up-right" class="size-4"></i>
                            </span>
                        </div>

                        {{-- bottom text block --}}
                        <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-8 text-white">
                            <h3 class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold leading-[1] mb-3">
                                {{ $p['name'] }}
                            </h3>
                            <p class="text-sm sm:text-base text-white/75 max-w-md mb-4">
                                {{ $p['desc'] }}
                            </p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($p['tags'] as $tag)
                                    <span class="text-[10px] sm:text-xs font-mono uppercase tracking-[0.12em] rounded-full px-2.5 py-1 bg-white/10 text-white/80 border border-white/15">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach

        </div>
    </div>
</section>
