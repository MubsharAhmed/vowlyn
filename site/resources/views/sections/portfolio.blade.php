{{-- ============================================================================
PORTFOLIO — "Real Projects, Measurable Outcomes"
Exo-Ape-inspired asymmetric image collage. Cards 2 & 4 parallax slowly while
cards 1 & 3 are anchored, giving the illusion of depth on scroll.
============================================================================ --}}
@php
    $projects = [
        [
            'category' => 'B2B Distribution',
            'num' => '01',
            'name' => 'Moventra Distribution',
            'desc' => 'A streamlined wholesale experience connecting global electronics inventory with retailers across international markets.',
            'tags' => ['B2B Commerce', 'Wholesale', 'Lead Generation'],
            'url' => 'https://www.moventradistribution.com/',
            'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?auto=format&fit=crop&w=1400&q=80',
            'alt' => 'Moventra Distribution wholesale electronics website project',
            'span' => 'lg:col-span-7',
            'aspect' => 'aspect-[5/6]',
            'offset' => '',
            'parallax' => false,
            'tone' => 'lavender',
        ],
        [
            'category' => 'E-Commerce Platform',
            'num' => '02',
            'name' => 'Meljori Jewellery',
            'desc' => 'A refined jewellery storefront designed to make product discovery feel considered, premium, and effortless.',
            'tags' => ['Jewellery', 'E-Commerce', 'Product UX'],
            'url' => 'https://www.meljorijewellery.ca/',
            'image' => 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?auto=format&fit=crop&w=1200&q=80',
            'alt' => 'Meljori Jewellery e-commerce website project',
            'span' => 'lg:col-span-5',
            'aspect' => 'aspect-[4/3] lg:aspect-[4/5]',
            'offset' => 'lg:mt-16',
            'parallax' => '0.12',
            'tone' => 'blush',
        ],
        [
            'category' => 'IT Services',
            'num' => '03',
            'name' => 'IT Bridges',
            'desc' => 'A clear, credible digital presence that turns complex technology services into an easy path to the right solution.',
            'tags' => ['IT Consulting', 'B2B Website', 'Service UX'],
            'url' => 'https://itbridges.ca/',
            'image' => 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=1200&q=80',
            'alt' => 'IT Bridges technology services website project',
            'span' => 'lg:col-span-5',
            'aspect' => 'aspect-[4/3] lg:aspect-[4/5]',
            'offset' => '',
            'parallax' => false,
            'tone' => 'ink',
        ],
        [
            'category' => 'E-Commerce Platform',
            'num' => '04',
            'name' => 'Persian Designer Rugs',
            'desc' => 'An elegant catalogue experience built to present distinctive rug collections with clarity and visual depth.',
            'tags' => ['Rug Catalogue', 'E-Commerce', 'Visual Design'],
            'url' => 'https://persiandesignerrugs.ca/',
            'image' => 'https://images.unsplash.com/photo-1600166898405-da9535204843?auto=format&fit=crop&w=1400&q=80',
            'alt' => 'Persian Designer Rugs e-commerce website project',
            'span' => 'lg:col-span-7',
            'aspect' => 'aspect-[5/6]',
            'offset' => 'lg:-mt-12',
            'parallax' => '0.10',
            'tone' => 'lavender',
        ],
        [
            'category' => 'E-Commerce Platform',
            'num' => '05',
            'name' => 'Arian Rugs',
            'desc' => 'A high-performing rug catalogue and commerce platform shaped around discovery, trust, and conversion.',
            'tags' => ['E-Commerce', 'Catalog UX', 'Organic Growth'],
            'url' => 'https://arianrugs.com/',
            'image' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1400&q=80',
            'alt' => 'Arian Rugs e-commerce store built by Vowlyn',
            'span' => 'lg:col-span-7',
            'aspect' => 'aspect-[5/6]',
            'offset' => '',
            'parallax' => false,
            'tone' => 'blush',
        ],
        [
            'category' => 'Service Website',
            'num' => '06',
            'name' => 'Burloak Painting',
            'desc' => 'A local-service website designed to turn residential and commercial painting searches into qualified enquiries.',
            'tags' => ['Service Platform', 'Local SEO', 'Lead Conversion'],
            'url' => 'https://burlingtonspainters.com/',
            'image' => 'https://images.unsplash.com/photo-1562259949-e8e7689d7828?auto=format&fit=crop&w=1200&q=80',
            'alt' => 'Burloak Painting service website project built by Vowlyn',
            'span' => 'lg:col-span-5',
            'aspect' => 'aspect-[4/3] lg:aspect-[4/5]',
            'offset' => 'lg:mt-16',
            'parallax' => '0.12',
            'tone' => 'ink',
        ],
    ];
@endphp

<section id="projects" class="section-vw">
    <div class="container-vw">

        {{-- ============ Header ============ --}}
        <div class="grid lg:grid-cols-[1fr_1fr] gap-10 mb-14 lg:mb-20 items-end">
            <div>
                <div class="eyebrow-row mb-5" data-reveal>
                    <span class="eyebrow">Portfolio</span>
                </div>
                <h2 class="headline-display text-4xl sm:text-5xl lg:text-6xl text-slate-900" data-reveal
                    data-reveal-delay="0.05">
                    Real Software Projects,<br /><span class="text-brand-gradient">Measurable Outcomes.</span>
                </h2>
            </div>
            <p class="text-on-surface/65 text-lg leading-relaxed max-w-md lg:justify-self-end" data-reveal
                data-reveal-delay="0.12">
                Every project below is a live product — web apps, e-commerce platforms, and operations systems
                engineered by our studio and measured by results, not screenshots.
                <a href="{{ route('portfolio') }}" class="inline-flex items-center gap-1.5 mt-3 text-primary-700 font-medium underline decoration-primary-300 underline-offset-4 hover:decoration-primary-600 transition">
                    Browse the full portfolio <i data-lucide="arrow-up-right" class="size-4"></i>
                </a>
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
                          {{ $p['offset'] }}">

                    {{-- Image well --}}
                    <div class="relative w-full {{ $p['aspect'] }} overflow-hidden rounded-3xl
                                bg-gradient-to-br from-lavender-200 via-white to-lavender-300">
                        <img src="{{ $p['image'] }}" alt="{{ $p['alt'] }}"
                             loading="lazy" width="1200" height="1500"
                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-[1200ms] ease-out group-hover:scale-[1.04]" />

                        {{-- gradient scrim for text legibility --}}
                        <div aria-hidden="true"
                             class="absolute inset-0 bg-gradient-to-t
                                    {{ $p['tone'] === 'ink' ? 'from-[#0d0420]/85 via-[#0d0420]/25 to-transparent' : 'from-[#160a2c]/80 via-[#160a2c]/20 to-transparent' }}">
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
