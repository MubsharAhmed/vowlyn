{{-- ============================================================================
SERVICE DETAIL — /services/{slug}
Reusable, SEO-first landing page driven by App\Support\ServiceCatalog.
============================================================================ --}}
@extends('layouts.app')

@section('title', $service['meta_title'])
@section('description', $service['meta_description'])
@section('canonical', route('services.show', $service['slug']))
@section('og_title', $service['meta_title'])
@section('og_description', $service['meta_description'])
@section('twitter_title', $service['meta_title'])
@section('twitter_description', $service['meta_description'])

@push('head')
    {{-- Breadcrumbs --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [
        { "@type": "ListItem", "position": 1, "name": "Home", "item": "{{ route('home') }}" },
        { "@type": "ListItem", "position": 2, "name": "Services", "item": "{{ route('services') }}" },
        { "@type": "ListItem", "position": 3, "name": @json($service['name'], JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP), "item": "{{ route('services.show', $service['slug']) }}" }
      ]
    }
    </script>

    {{-- Service --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": "Service",
      "serviceType": @json($service['name'], JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP),
      "name": @json($service['name'].' — Vowlyn', JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP),
      "description": @json($service['meta_description'], JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP),
      "url": "{{ route('services.show', $service['slug']) }}",
      "provider": {
        "@type": "Organization",
        "name": "Vowlyn",
        "url": "{{ route('home') }}",
        "logo": "{{ asset('brand/vowlyn-logo.png') }}"
      },
      "areaServed": ["North America", "Europe", "MENA"]
    }
    </script>

    {{-- FAQ --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        @foreach ($service['faqs'] as $i => $f)
        { "@type": "Question", "name": @json($f['q'], JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP), "acceptedAnswer": { "@type": "Answer", "text": @json($f['a'], JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP) } }@if(!$loop->last),@endif
        @endforeach
      ]
    }
    </script>
@endpush

@section('content')
    {{-- ═══════════════════════════ HERO ═══════════════════════════ --}}
    <section class="relative bg-brand-stage text-white overflow-hidden grain pt-28 lg:pt-36 pb-16 lg:pb-24">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-24 -right-16 size-[30rem] rounded-full bg-primary-700/30 blur-[120px]"></div>
            <div class="absolute bottom-0 -left-24 size-[26rem] rounded-full bg-blush-500/20 blur-[120px]"></div>
        </div>

        <div class="container-vw relative">
            {{-- Breadcrumb (visible) --}}
            <nav aria-label="Breadcrumb" class="mb-8" data-reveal>
                <ol class="flex items-center gap-2 text-[11px] font-mono uppercase tracking-[0.18em] text-white/45">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('services') }}" class="hover:text-white transition-colors">Services</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-white/80">{{ $service['name'] }}</li>
                </ol>
            </nav>

            <div class="grid lg:grid-cols-[1.15fr_0.85fr] gap-12 lg:gap-16 items-center">
                <div>
                    <div class="flex items-center gap-3 mb-6" data-reveal>
                        <span class="size-11 grid place-items-center rounded-xl bg-gradient-to-br from-primary-500/25 to-blush-500/10 border border-white/12">
                            <i data-lucide="{{ $service['icon'] }}" class="size-5 text-white"></i>
                        </span>
                        <span class="eyebrow !text-primary-300">{{ $service['eyebrow'] }}</span>
                    </div>

                    <h1 class="headline-display text-4xl sm:text-5xl lg:text-6xl text-white leading-[0.98]" data-reveal data-reveal-delay="0.05">
                        {{ $service['h1'] }}<br class="hidden sm:block" />
                        <span class="text-brand-gradient">{{ $service['headline_accent'] }}</span>
                    </h1>

                    <p class="mt-7 text-lg lg:text-xl text-white/65 max-w-xl leading-relaxed" data-reveal data-reveal-delay="0.1">
                        {{ $service['lede'] }}
                    </p>

                    <div class="mt-9 flex flex-wrap items-center gap-4" data-reveal data-reveal-delay="0.15">
                        <a href="{{ url('/#contact') }}" data-magnetic="0.2" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-white text-[#0d0420] font-medium hover:bg-white/90 transition-colors">
                            Start a project <i data-lucide="arrow-right" class="size-4"></i>
                        </a>
                        <a href="{{ route('portfolio') }}" class="inline-flex items-center gap-2 text-sm font-mono uppercase tracking-[0.16em] text-white/70 hover:text-white transition-colors">
                            See our work <i data-lucide="arrow-up-right" class="size-4"></i>
                        </a>
                    </div>
                </div>

                {{-- Outcome chips --}}
                <div class="grid grid-cols-3 lg:grid-cols-1 gap-4" data-stagger="0.1">
                    @foreach ($service['outcomes'] as $o)
                        <div data-stagger-item class="rounded-2xl p-5 lg:px-6 lg:py-5 border border-white/10 bg-[#160828]/60 backdrop-blur-xl flex flex-col lg:flex-row lg:items-center gap-1 lg:gap-4">
                            <span class="font-display text-3xl lg:text-4xl font-bold text-brand-gradient leading-none shrink-0">{{ $o['v'] }}</span>
                            <span class="text-[11px] lg:text-sm font-mono uppercase tracking-[0.16em] text-white/50 leading-snug">{{ $o['l'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════ WHAT WE BUILD ═══════════════════════ --}}
    <section class="relative bg-mesh-light section-vw overflow-hidden">
        <div class="container-vw">
            <div class="max-w-2xl mb-12 lg:mb-16">
                <span class="eyebrow !text-primary-700" data-reveal>What we build</span>
                <h2 class="headline-display text-3xl lg:text-5xl mt-3" data-reveal data-reveal-delay="0.05">
                    {{ $service['name'] }}, end to end.
                </h2>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-6" data-stagger="0.08">
                @foreach ($service['build'] as $b)
                    <article data-stagger-item data-spotlight class="card-spotlight group relative rounded-2xl p-6 bg-white border border-on-surface/10 shadow-[0_20px_50px_-28px_rgba(75,0,130,0.18)] transition-all duration-500 hover:-translate-y-1.5 hover:shadow-[0_30px_70px_-30px_rgba(75,0,130,0.28)]">
                        <span class="relative size-12 grid place-items-center rounded-xl bg-primary-50 border border-primary-100 mb-5 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6">
                            <i data-lucide="{{ $b['icon'] }}" class="size-5 text-primary-700"></i>
                        </span>
                        <h3 class="relative font-display text-lg font-semibold text-on-surface leading-tight">{{ $b['title'] }}</h3>
                        <p class="relative mt-2 text-sm text-on-surface-muted leading-relaxed">{{ $b['desc'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════ PROCESS ═══════════════════════════ --}}
    <section class="relative bg-brand-stage text-white overflow-hidden grain section-vw">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 left-1/3 size-[28rem] rounded-full bg-primary-700/25 blur-[120px]"></div>
        </div>
        <div class="container-vw relative">
            <div class="max-w-2xl mb-14 lg:mb-20">
                <span class="eyebrow !text-primary-300" data-reveal>How we deliver</span>
                <h2 class="headline-display text-3xl lg:text-5xl mt-3" data-reveal data-reveal-delay="0.05">
                    A clear path from<br /><span class="text-brand-gradient">idea to launch.</span>
                </h2>
            </div>

            <div class="relative grid md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-6" data-stagger="0.1">
                <div aria-hidden="true" class="hidden lg:block absolute top-6 left-[12%] right-[12%] h-px bg-gradient-to-r from-primary-300/30 via-blush-300/40 to-primary-300/30"></div>
                @foreach ($service['process'] as $i => $s)
                    <div data-stagger-item class="relative">
                        <div class="flex items-center gap-4 mb-5">
                            <span class="relative z-10 size-12 grid place-items-center rounded-full bg-[#160828] border border-white/15 font-display font-bold text-lg text-brand-gradient">{{ sprintf('%02d', $i + 1) }}</span>
                        </div>
                        <h3 class="font-display text-xl font-semibold">{{ $s['t'] }}</h3>
                        <p class="mt-2 text-sm text-white/60 leading-relaxed">{{ $s['d'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════ STACK ═══════════════════════════ --}}
    <section class="relative bg-mesh-light section-vw overflow-hidden">
        <div class="container-vw">
            <div class="grid lg:grid-cols-[0.9fr_1.1fr] gap-10 lg:gap-16 items-center">
                <div data-reveal>
                    <span class="eyebrow !text-primary-700">The toolkit</span>
                    <h2 class="headline-display text-3xl lg:text-4xl mt-3">Proven tools, chosen for fit.</h2>
                    <p class="text-on-surface-muted mt-4 leading-relaxed">
                        We pick technology for your problem, not our comfort — mature, well-supported tools your team can hire for and maintain long after launch.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3" data-stagger="0.04">
                    @foreach ($service['stack'] as $tech)
                        <span data-stagger-item class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full border border-on-surface/12 bg-white text-sm font-medium text-on-surface shadow-sm">
                            <span class="size-1.5 rounded-full bg-gradient-to-br from-primary-500 to-blush-500"></span>
                            {{ $tech }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════ FAQ ═══════════════════════════ --}}
    <section class="relative bg-mesh-light pb-24 lg:pb-32 overflow-hidden">
        <div class="container-vw">
            <div class="grid lg:grid-cols-[0.8fr_1.2fr] gap-10 lg:gap-16 items-start">
                <div data-reveal>
                    <span class="eyebrow !text-primary-700">FAQ</span>
                    <h2 class="headline-display text-3xl lg:text-4xl mt-3">{{ $service['name'] }} questions.</h2>
                    <p class="text-on-surface-muted mt-4 leading-relaxed">Can't find your answer? A 30-minute call sorts it out fast.</p>
                    <a href="https://calendly.com/junaidswati/new-meeting" target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-6">Talk to us</a>
                </div>

                <div class="divide-y divide-on-surface/10 border-y border-on-surface/10" x-data="{ open: 0 }">
                    @foreach ($service['faqs'] as $i => $f)
                        <div data-reveal data-reveal-delay="{{ $i * 0.04 }}">
                            <button type="button" @click="open === {{ $i }} ? open = null : open = {{ $i }}" class="w-full flex items-center justify-between gap-4 py-5 lg:py-6 text-left group">
                                <span class="font-display text-lg lg:text-xl text-on-surface group-hover:text-primary-700 transition-colors">{{ $f['q'] }}</span>
                                <span class="size-8 grid place-items-center rounded-full border border-on-surface/15 shrink-0 transition-all duration-300" :class="open === {{ $i }} ? 'bg-primary-700 text-white border-transparent rotate-180' : 'text-on-surface'">
                                    <i data-lucide="chevron-down" class="size-4"></i>
                                </span>
                            </button>
                            <div x-show="open === {{ $i }}" x-collapse x-cloak>
                                <p class="pb-6 pr-8 text-on-surface-muted leading-relaxed">{{ $f['a'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════ RELATED SERVICES ═══════════════════════ --}}
    @if (!empty($related))
        <section class="relative bg-brand-stage text-white overflow-hidden grain section-vw">
            <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
                <div class="absolute -bottom-24 -right-16 size-[26rem] rounded-full bg-primary-700/30 blur-[120px]"></div>
            </div>
            <div class="container-vw relative">
                <div class="flex items-end justify-between flex-wrap gap-4 mb-12">
                    <div>
                        <span class="eyebrow !text-primary-300" data-reveal>Explore more</span>
                        <h2 class="headline-display text-3xl lg:text-4xl mt-3" data-reveal data-reveal-delay="0.05">Related services</h2>
                    </div>
                    <a href="{{ route('services') }}" class="inline-flex items-center gap-2 text-sm font-mono uppercase tracking-[0.16em] text-white/70 hover:text-white transition-colors">
                        All services <i data-lucide="arrow-right" class="size-4"></i>
                    </a>
                </div>

                <div class="grid md:grid-cols-3 gap-5 lg:gap-6" data-stagger="0.1">
                    @foreach ($related as $r)
                        <a href="{{ route('services.show', $r['slug']) }}" data-stagger-item class="group relative rounded-2xl p-6 lg:p-7 border border-white/10 bg-[#160828]/60 backdrop-blur-xl transition-all duration-500 hover:-translate-y-1.5 hover:border-primary-300/50">
                            <div class="flex items-start justify-between mb-6">
                                <span class="size-12 grid place-items-center rounded-xl bg-gradient-to-br from-primary-500/25 to-blush-500/10 border border-white/12 transition-transform duration-500 group-hover:scale-110">
                                    <i data-lucide="{{ $r['icon'] }}" class="size-5 text-white"></i>
                                </span>
                                <span class="size-8 rounded-full grid place-items-center bg-white/5 border border-white/10 transition-all duration-500 group-hover:bg-white group-hover:text-[#0d0420] group-hover:-rotate-45">
                                    <i data-lucide="arrow-right" class="size-3.5"></i>
                                </span>
                            </div>
                            <h3 class="font-display text-lg font-semibold">{{ $r['name'] }}</h3>
                            <p class="mt-2 text-sm text-white/55 leading-relaxed">{{ $r['meta_description'] }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════ CTA ═══════════════════════════ --}}
    <section class="relative bg-mesh-light section-vw overflow-hidden">
        <div class="container-vw max-w-4xl text-center">
            <h2 class="headline-display text-3xl lg:text-5xl" data-reveal>
                Ready to build your <span class="text-brand-gradient">{{ Illuminate\Support\Str::lower($service['name']) }}</span>?
            </h2>
            <p class="mt-6 text-lg text-on-surface-muted max-w-xl mx-auto leading-relaxed" data-reveal data-reveal-delay="0.08">
                Tell us what you're building. We reply within 24 hours — and every engagement starts with a fixed-scope discovery sprint.
            </p>
            <div class="mt-9 flex items-center justify-center gap-4 flex-wrap" data-reveal data-reveal-delay="0.12">
                <a href="{{ url('/#contact') }}" class="btn btn-primary">Start a project</a>
                <a href="{{ route('services') }}" class="inline-flex items-center gap-2 text-sm font-mono uppercase tracking-[0.16em] text-on-surface hover:text-primary-700 transition-colors">
                    Back to all services →
                </a>
            </div>
        </div>
    </section>
@endsection
