{{-- ============================================================================
ABOUT — "The Principal & The Crew"
Hero opens on the founder (Junaid Swati). The Crew section presents the team
as an org-tree by department: Engineering (Lead + 2 reports), Studio (2),
Growth (2). SVG hierarchy lines draw on scroll; cards reveal in stagger;
hover-tilt on the avatar plates for a tactile feel.
============================================================================ --}}
@extends('layouts.app')

@section('title', 'About Vowlyn — A Senior Software Development Studio')
@section('description', 'Meet Vowlyn: an eight-person software development studio led by founder Junaid Swati, with in-house engineering, studio, and growth pods directing eight brands end-to-end.')
@section('canonical', 'https://vowlyn.com/about')
@section('og_type', 'profile')
@section('og_title', 'About Vowlyn — A Senior Software Development Studio')
@section('og_description', 'An eight-person software studio led by Junaid Swati — engineering, studio, and growth under one roof.')
@section('twitter_title', 'About Vowlyn — A Senior Software Development Studio')
@section('twitter_description', 'An eight-person software studio led by Junaid Swati — engineering, studio, and growth under one roof.')

@push('head')
    {{-- AboutPage + Organization — the highest-value E-E-A-T schema on the site --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": "AboutPage",
      "url": "https://vowlyn.com/about",
      "mainEntity": {
        "@type": "Organization",
        "@id": "https://vowlyn.com/#organization",
        "name": "Vowlyn",
        "description": "A senior software development studio of eight specialists — engineering, studio, and growth — directing eight brands end-to-end.",
        "foundingDate": "2016",
        "numberOfEmployees": "8",
        "founder": {
          "@type": "Person",
          "name": "Junaid Swati",
          "jobTitle": "Founder & Project Director"
        },
        "employee": [
          { "@type": "Person", "name": "Mubashar Ahmed Khan", "jobTitle": "Technical Team Lead · Web Developer" },
          { "@type": "Person", "name": "Asif", "jobTitle": "Full-Stack Engineer" },
          { "@type": "Person", "name": "Atif", "jobTitle": "Frontend Engineer" },
          { "@type": "Person", "name": "Asjid Rouf", "jobTitle": "Graphic & Motion Designer" },
          { "@type": "Person", "name": "Yoruu", "jobTitle": "Video Editor" },
          { "@type": "Person", "name": "Yahya Rasheed", "jobTitle": "SEO & Content Strategist" },
          { "@type": "Person", "name": "Ayesha Younas", "jobTitle": "Research & Lead-Gen Specialist" }
        ]
      }
    }
    </script>
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        { "@type": "Question", "name": "Who is Junaid Swati?", "acceptedAnswer": { "@type": "Answer", "text": "Junaid Swati is the founder of Vowlyn, leading delivery and stakeholder strategy across every engagement. Over a decade he has helped build and direct eight brands." } },
        { "@type": "Question", "name": "How big is the Vowlyn team?", "acceptedAnswer": { "@type": "Answer", "text": "Eight specialists across three in-house pods (engineering, studio, and growth) with no outsourced contractors and no middle-management layer." } },
        { "@type": "Question", "name": "Is Vowlyn a real studio with a real team?", "acceptedAnswer": { "@type": "Answer", "text": "Yes. Vowlyn is a focused room of eight named specialists who own design, code, motion, and growth for every brand it directs." } },
        { "@type": "Question", "name": "Where is Vowlyn based and who does it serve?", "acceptedAnswer": { "@type": "Answer", "text": "Vowlyn began serving local Burlington brands and now directs work across North America, Europe, and the MENA region." } }
      ]
    }
    </script>
@endpush

@php
    $founder = [
        'name'   => 'Junaid Swati',
        'first'  => 'Junaid',
        'last'   => 'Swati',
        'init'   => 'JS',
        'role'   => 'Project Management · SME Digital Solution Partner',
        'bio'    => "Vowlyn is a senior software development studio of eight specialists — engineering, studio, and growth — and I lead delivery across every engagement. Over the past decade I've helped build and direct eight brands, from boutique studios to multi-city buying groups, keeping strategy, design, and engineering pointed at the same outcome.",
        'brands' => [
            'Vowlyn', 'Vowlyn Weddings', 'Burlington Painters', 'Arian Rugs',
            'United Buying Group', 'Ontario Buying Group', 'Alpha Buying Association', 'Square 21 Marketing',
        ],
    ];

    // Engineering has a clear hierarchy: Mubashar leads, Asif + Atif report.
    $engineering = [
        'code' => 'ENG',
        'name' => 'Engineering',
        'tag'  => 'Code, infrastructure, integrations.',
        'lead' => [
            'name'  => 'Mubashar Ahmed Khan',
            'role'  => 'Technical Team Lead · Web Developer',
            'init'  => 'MK',
            'motto' => 'Architects every release we ship.',
            'tone'  => ['#18d2ff', '#5c7cf5'],
        ],
        'team' => [
            ['name' => 'Asif', 'role' => 'Full-Stack Engineer', 'init' => 'AS', 'motto' => 'Builds the systems other systems lean on.', 'tone' => ['#5c7cf5', '#7b41b3']],
            ['name' => 'Atif', 'role' => 'Full-Stack Engineer', 'init' => 'AT', 'motto' => 'Lives in the seam between design and code.', 'tone' => ['#7b41b3', '#c8459b']],
        ],
    ];

    $studio = [
        'code'   => 'STU',
        'name'   => 'Studio',
        'tag'    => 'Brand, motion, video.',
        'people' => [
            ['name' => 'Asjid Rouf', 'role' => 'Graphic Designer · Motion Graphics', 'init' => 'AR', 'motto' => 'Turns ideas into motion.',  'tone' => ['#c8459b', '#ff7ab8']],
            ['name' => 'Yoruu',      'role' => 'Video Editor',                       'init' => 'YO', 'motto' => 'Edits the cuts that land.', 'tone' => ['#ff7ab8', '#ef4444']],
        ],
    ];

    $growth = [
        'code'   => 'GRW',
        'name'   => 'Growth',
        'tag'    => 'Search, content, demand.',
        'people' => [
            ['name' => 'Yahya Rasheed',  'role' => 'SEO · Content Strategist (Web & Social)', 'init' => 'YR', 'motto' => 'Writes what search reads and people remember.', 'tone' => ['#18d2ff', '#c8459b']],
            ['name' => 'Ayesha Younas', 'role' => 'Research Assistant · Lead-Gen Specialist','init' => 'AY', 'motto' => 'Hunts the leads that move pipelines.',          'tone' => ['#5c7cf5', '#ff7ab8']],
        ],
    ];

    /* ─────────────────────────────────────────────────────────────
       FEATURED SPECIALISTS — extended profiles for the picker UI.
       Each member is rendered as a premium editorial spread, picked
       from the sticky sidebar on the left.
       ───────────────────────────────────────────────────────────── */
    $specialists = [
        [
            'init' => 'MK', 'name' => 'Mubashar Ahmed Khan', 'first' => 'Mubashar', 'dept' => 'Engineering', 'code' => 'ENG',
            'role' => 'Technical Team Lead · Web Developer',
            'badge' => 'Lead',
            'tone' => ['#18d2ff', '#5c7cf5'],
            'years' => '10+', 'projects' => '48', 'stack_count' => '12',
            'intro' => "Mubashar leads engineering at Vowlyn — owning architecture, code review, and the technical health of every project we ship. He is the last set of eyes on every release and the first call when an integration gets gnarly.",
            'specialties' => ['System Architecture', 'Laravel', 'API Design', 'DevOps', 'Code Review', 'Team Mentorship'],
            'signature' => 'Multi-tenant CRM for United Buying Group — 7 brands on one codebase.',
            'quote' => 'Every line of code is a promise to whoever maintains it next.',
        ],
        [
            'init' => 'AS', 'name' => 'Asif', 'first' => 'Asif', 'dept' => 'Engineering', 'code' => 'ENG',
            'role' => 'Full-Stack Engineer',
            'tone' => ['#5c7cf5', '#7b41b3'],
            'years' => '6+', 'projects' => '32', 'stack_count' => '10',
            'intro' => "Asif builds the backend systems the rest of the studio relies on — from REST APIs to admin panels — with a focus on shipping the simplest thing that survives in production.",
            'specialties' => ['Laravel', 'Vue', 'PostgreSQL', 'Queue Workers', 'Auth & RBAC', 'Filament'],
            'signature' => 'Arian Rugs inventory + sales platform — five years uptime.',
            'quote' => "The simplest solution that works in production wins.",
        ],
        [
            'init' => 'AT', 'name' => 'Atif', 'first' => 'Atif', 'dept' => 'Engineering', 'code' => 'ENG',
            'role' => 'Full-Stack Engineer',
            'tone' => ['#7b41b3', '#c8459b'],
            'years' => '5+', 'projects' => '27', 'stack_count' => '11',
            'intro' => "Atif lives where engineering meets design — building interfaces that are as performant as they are polished. He's the bridge between what the studio designs and what actually ships to production.",
            'specialties' => ['Laravel', 'Tailwind CSS', 'Alpine.js', 'GSAP', 'Three.js', 'Lighthouse'],
            'signature' => 'vowlyn.com — the very site you are reading.',
            'quote' => 'Pixels should pay rent.',
        ],
        [
            'init' => 'AR', 'name' => 'Asjid Rouf', 'first' => 'Asjid', 'dept' => 'Studio', 'code' => 'STU',
            'role' => 'Graphic Designer · Motion Graphics',
            'tone' => ['#c8459b', '#ff7ab8'],
            'years' => '7+', 'projects' => '60', 'stack_count' => '8',
            'intro' => "Asjid runs visual identity and motion across every brand we direct — from a logo mark to a launch reel. If it has a logo, a frame rate, or a colour story, it passes through Asjid.",
            'specialties' => ['Brand Identity', 'After Effects', 'Illustrator', 'Figma', 'Motion Design', '3D Mockups'],
            'signature' => 'Vowlyn Weddings rebrand — 0 to identity system in 6 weeks.',
            'quote' => "Design that doesn't move doesn't breathe.",
        ],
        [
            'init' => 'YO', 'name' => 'Yoruu', 'first' => 'Yoruu', 'dept' => 'Studio', 'code' => 'STU',
            'role' => 'Video Editor',
            'tone' => ['#ff7ab8', '#ef4444'],
            'years' => '5+', 'projects' => '120', 'stack_count' => '6',
            'intro' => "Yoruu edits every video that leaves the studio — long-form campaign films, short social cuts, and the launch reels that pair with every brand rollout.",
            'specialties' => ['Premiere Pro', 'DaVinci Resolve', 'Colour Grading', 'Sound Design', 'Short-Form', 'Long-Form'],
            'signature' => 'Burlington Painters brand film — 6 figure organic reach.',
            'quote' => "The cut you don't notice is the cut that worked.",
        ],
        [
            'init' => 'YR', 'name' => 'Yahya Rasheed', 'first' => 'Yahya', 'dept' => 'Growth', 'code' => 'GRW',
            'role' => 'SEO · Content Strategist (Web & Social)',
            'tone' => ['#18d2ff', '#c8459b'],
            'years' => '6+', 'projects' => '40', 'stack_count' => '9',
            'intro' => "Yahya owns SEO and content strategy across the directed brands — from keyword research and topical maps to the cadence of what gets published on web and social.",
            'specialties' => ['SEO Strategy', 'Content Mapping', 'Ahrefs', 'GA4', 'Editorial Calendar', 'Social Strategy'],
            'signature' => 'Arian Rugs organic search — 10× sessions in 12 months.',
            'quote' => 'Words that rank are still words that read.',
        ],
        [
            'init' => 'AY', 'name' => 'Ayesha Younas', 'first' => 'Ayesha', 'dept' => 'Growth', 'code' => 'GRW',
            'role' => 'Research Assistant · Lead-Gen Specialist',
            'tone' => ['#5c7cf5', '#ff7ab8'],
            'years' => '4+', 'projects' => '35', 'stack_count' => '7',
            'intro' => "Ayesha runs research and lead-gen — surfacing the prospects that turn into actual conversations, and the data that keeps every outreach campaign aimed at the right room.",
            'specialties' => ['Lead Research', 'B2B Outreach', 'HubSpot', 'Apollo.io', 'Qualifying', 'CRM Hygiene'],
            'signature' => 'United Buying Group sales pipeline — 4× qualified leads.',
            'quote' => 'Quality leads beat noisy lists every time.',
        ],
    ];

    /* ─────────────────────────────────────────────────────────────
       TIMELINE — placed at the end of the page as the studio history
       ───────────────────────────────────────────────────────────── */
    $timeline = [
        ['year' => '2016', 'title' => 'Founded by Junaid Swati',  'note' => 'Started as a one-person consultancy serving local Burlington brands with web + project delivery.'],
        ['year' => '2018', 'title' => 'First buying-group client', 'note' => 'Shipped a multi-stakeholder portal for United Buying Group — the first multi-brand engagement.'],
        ['year' => '2020', 'title' => 'Engineering arm formed',    'note' => 'Brought engineering in-house. Mubashar joined as Technical Team Lead — the first full-stack pod formed alongside delivery.'],
        ['year' => '2022', 'title' => 'Studio + motion added',     'note' => 'Design, motion graphics and video editing joined the room — ending the agency-collab era for good.'],
        ['year' => '2024', 'title' => 'Growth team in place',      'note' => 'SEO, content and lead-gen brought in-house. End-to-end engagements possible for every brand we direct.'],
        ['year' => '2026', 'title' => "You're reading this",       'note' => 'A focused room of eight specialists operating across eight active brands — and still hiring slowly.'],
    ];
@endphp

@section('content')
    {{-- ═══ Scroll-driven reading-progress bar ═══ --}}
    <div class="fixed top-0 inset-x-0 z-50 h-[3px] bg-transparent pointer-events-none">
        <div class="h-full origin-left scale-x-0 bg-gradient-to-r from-primary-400 via-blush-400 to-primary-700" data-reading-progress></div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════
         § 01 · THE PRINCIPAL — Founder is the page hero
         ──────────────────────────────────────────────────────────────────── --}}
    <section class="relative bg-mesh-light pt-32 lg:pt-44 pb-24 lg:pb-32 overflow-hidden grain">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-40 -right-32 size-[34rem] rounded-full bg-blush-300/35 blur-[120px]"></div>
            <div class="absolute -bottom-32 -left-24 size-[28rem] rounded-full bg-primary-300/30 blur-[120px]"></div>
        </div>

        <div class="container-vw relative">
            {{-- Masthead strip --}}
            <div class="flex items-center gap-4 mb-12 lg:mb-16" data-reveal>
                <span class="font-mono text-[10px] uppercase tracking-[0.22em] text-on-surface-muted whitespace-nowrap">The Principal</span>
                <span class="h-px flex-1 bg-on-surface/15"></span>
                <span class="font-mono text-[10px] uppercase tracking-[0.22em] text-on-surface-muted whitespace-nowrap">Burlington, ON · Est. 2016</span>
            </div>

            <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                {{-- LEFT · Portrait plate --}}
                <div class="lg:col-span-5 order-2 lg:order-1" data-reveal>
                    <div class="relative max-w-md mx-auto lg:mx-0" data-tilt>
                        {{-- Corner registration marks --}}
                        <span aria-hidden="true" class="absolute -top-3 -left-3 size-6 border-l-2 border-t-2 border-on-surface"></span>
                        <span aria-hidden="true" class="absolute -top-3 -right-3 size-6 border-r-2 border-t-2 border-on-surface"></span>
                        <span aria-hidden="true" class="absolute -bottom-3 -left-3 size-6 border-l-2 border-b-2 border-on-surface"></span>
                        <span aria-hidden="true" class="absolute -bottom-3 -right-3 size-6 border-r-2 border-b-2 border-on-surface"></span>

                        {{-- Photo plate --}}
                        <figure class="relative aspect-[4/5] bg-white border border-on-surface/10 shadow-[0_40px_100px_-30px_rgba(15,15,32,0.55)] rounded-sm overflow-hidden">
                            <div aria-hidden="true" class="absolute inset-0 plate-grid opacity-25"></div>
                            <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-br from-primary-50/60 via-transparent to-blush-100/40"></div>

                            {{-- Swap for <img src="{{ asset('team/junaid.jpg') }}" class="absolute inset-0 w-full h-full object-cover"/> --}}
                            <div class="absolute inset-0 grid place-items-center">
                                <span class="font-display font-bold text-[14rem] sm:text-[16rem] leading-none bg-gradient-to-br from-primary-500 via-blush-500 to-primary-700 bg-clip-text text-transparent select-none translate-y-[-0.05em]">{{ $founder['init'] }}</span>
                            </div>

                            {{-- Top meta strip --}}
                            <figcaption class="absolute top-3 left-3 right-3 flex items-center justify-between text-[9px] font-mono uppercase tracking-[0.22em] text-on-surface/55">
                                <span>Principal · 001</span>
                                <span class="flex items-center gap-1.5">
                                    <span class="size-1.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                                    Active
                                </span>
                                <span>Est. 2016</span>
                            </figcaption>

                            {{-- Signature footer --}}
                            <div class="absolute bottom-0 inset-x-0 px-4 pb-4 pt-12 bg-gradient-to-t from-white via-white/90 to-transparent">
                                <div class="flex items-end justify-between gap-3">
                                    <div>
                                        <p class="font-mono text-[9px] uppercase tracking-[0.22em] text-on-surface-muted">Signed</p>
                                        <p class="font-display italic text-2xl text-on-surface leading-none mt-1">{{ $founder['first'] }}.</p>
                                    </div>
                                    <p class="font-mono text-[9px] uppercase tracking-[0.22em] text-on-surface-muted">{{ $founder['init'] }}-001</p>
                                </div>
                            </div>

                            <span aria-hidden="true" class="absolute bottom-0 inset-x-0 h-1 bg-gradient-to-r from-primary-400 via-blush-500 to-primary-700"></span>
                        </figure>

                        {{-- Floating credentials chip --}}
                        <div class="absolute -bottom-5 -right-5 lg:-right-8 px-4 py-3 rounded-2xl bg-on-surface text-white shadow-2xl shadow-on-surface/30 rotate-3">
                            <p class="font-mono text-[9px] uppercase tracking-[0.22em] text-white/60">Directing</p>
                            <p class="font-display text-2xl leading-none mt-1">8 brands</p>
                        </div>
                    </div>
                </div>

                {{-- RIGHT · Editorial intro --}}
                <div class="lg:col-span-7 order-1 lg:order-2" data-reveal data-reveal-delay="0.1">
                    <p class="eyebrow !text-primary-700 mb-5">Hi there.</p>

                    <h1 class="font-display font-bold text-on-surface text-[clamp(2.75rem,8vw,6rem)] leading-[0.9] tracking-tight">
                        I'm <span class="bg-gradient-to-r from-primary-500 via-blush-500 to-primary-700 bg-clip-text text-transparent">{{ $founder['first'] }}</span><br class="hidden sm:block"/>
                        <span class="font-medium italic text-on-surface-muted">{{ $founder['last'] }}.</span>
                    </h1>

                    <p class="mt-7 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-on-surface text-white text-[11px] font-mono uppercase tracking-[0.2em]">
                        <span class="size-1.5 rounded-full bg-primary-300 inline-block"></span>
                        {{ $founder['role'] }}
                    </p>

                    <p class="mt-8 text-lg lg:text-xl text-on-surface-muted leading-relaxed max-w-2xl">
                        {{ $founder['bio'] }}
                    </p>

                    {{-- Quick Answer (GEO) --}}
                    <x-quick-answer class="mt-8 max-w-2xl" data-reveal data-reveal-delay="0.15">
                        Vowlyn is an eight-person software development studio led by founder Junaid Swati. It runs
                        three in-house pods — engineering, studio, and growth — with no outsourced contractors and no
                        middle-management layer. The same team owns design, code, motion, and growth for every brand
                        it directs, from kickoff to maintenance.
                    </x-quick-answer>

                    {{-- Brand roster --}}
                    <div class="mt-10">
                        <p class="font-mono text-[10px] uppercase tracking-[0.22em] text-on-surface-muted mb-3">Brands under direction</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($founder['brands'] as $i => $b)
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-on-surface/15 bg-white/70 backdrop-blur-sm text-sm text-on-surface hover:border-on-surface/35 transition-colors">
                                    <span class="font-mono text-[9px] text-on-surface-muted">0{{ $i + 1 }}</span>
                                    {{ $b }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-10 flex flex-wrap items-center gap-4">
                        <a href="https://calendly.com/junaidswati/new-meeting" target="_blank" rel="noopener noreferrer" class="btn-primary">Book a 30-min discovery</a>
                        <a href="#crew" class="inline-flex items-center gap-2 text-sm font-mono uppercase tracking-[0.18em] text-on-surface hover:text-primary-700 transition-colors">
                            Meet the crew
                            <span aria-hidden="true">↓</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════════════════
         § 02 · THE CREW — Departments with org hierarchy
         ──────────────────────────────────────────────────────────────────── --}}
    <section id="crew" class="relative bg-brand-stage text-white py-24 lg:py-32 overflow-hidden grain">
        {{-- Ambient blobs --}}
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute top-1/4 -left-32 size-[24rem] rounded-full bg-primary-700/30 blur-3xl"></div>
            <div class="absolute bottom-1/4 -right-32 size-[26rem] rounded-full bg-blush-500/20 blur-3xl"></div>
        </div>

        <div class="container-vw relative">
            {{-- Section masthead --}}
            <div class="flex items-center gap-4 mb-10" data-reveal>
                <span class="font-mono text-[10px] uppercase tracking-[0.22em] text-white/55 whitespace-nowrap">The Crew</span>
                <span class="h-px flex-1 bg-white/15"></span>
                <span class="font-mono text-[10px] uppercase tracking-[0.22em] text-white/55 whitespace-nowrap">07 specialists · 03 departments</span>
            </div>

            <div class="flex items-end justify-between mb-16 flex-wrap gap-6">
                <h2 class="font-display text-[clamp(2.5rem,6vw,5rem)] leading-[0.95] tracking-tight max-w-3xl" data-reveal>
                    A flat studio with a<br/>
                    <span class="italic font-medium text-white/65">clear chain of command.</span>
                </h2>
                <p class="text-white/65 max-w-sm leading-relaxed" data-reveal data-reveal-delay="0.08">
                    Engineering reports into a single technical lead. Studio and growth run as flat pods. No middle layer sits between you and the people shipping your work — every project passes through this room, no outsourced contractors-of-the-week.
                </p>
            </div>

            {{-- ── Department grid ─────────────────────────────────────── --}}
            <div class="grid lg:grid-cols-12 gap-6 lg:gap-8">

                {{-- ── ENGINEERING column (lead + 2 reports, animated SVG line) ── --}}
                <article class="lg:col-span-6 relative rounded-3xl bg-white/[0.03] border border-white/10 p-6 lg:p-8 backdrop-blur-sm" data-reveal>
                    {{-- Column header --}}
                    <header class="flex items-start justify-between mb-8 pb-6 border-b border-white/10">
                        <div>
                            <p class="font-mono text-[10px] uppercase tracking-[0.22em] text-primary-300 mb-2">Dept · {{ $engineering['code'] }} · 03 people</p>
                            <h3 class="font-display text-3xl lg:text-4xl text-white">{{ $engineering['name'] }}</h3>
                            <p class="text-white/55 text-sm mt-1.5">{{ $engineering['tag'] }}</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-400/15 text-emerald-300 text-[10px] font-mono uppercase tracking-[0.18em]">
                            <span class="size-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Shipping
                        </span>
                    </header>

                    {{-- LEAD CARD (full width, larger) --}}
                    @php $L = $engineering['lead']; @endphp
                    <div class="relative" data-stagger-item>
                        <div class="relative rounded-2xl overflow-hidden border border-white/10 bg-[#160828]/70 backdrop-blur-xl p-5 group/lead crew-card" data-tilt>
                            {{-- Lead badge --}}
                            <span class="absolute top-4 right-4 z-10 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gradient-to-r from-primary-500 to-blush-500 text-white text-[9px] font-mono uppercase tracking-[0.22em]">
                                ★ Lead
                            </span>
                            <div aria-hidden="true" class="absolute inset-0 plate-grid opacity-20"></div>
                            <div aria-hidden="true" class="absolute -top-12 -right-12 size-44 rounded-full blur-3xl opacity-40 transition-opacity duration-500 group-hover/lead:opacity-70"
                                 style="background: radial-gradient(circle, {{ $L['tone'][0] }}, transparent 70%);"></div>

                            <div class="relative flex items-center gap-5">
                                <span class="grid place-items-center size-20 rounded-2xl text-white font-display font-bold text-2xl shadow-xl shrink-0"
                                      style="background: linear-gradient(135deg, {{ $L['tone'][0] }}, {{ $L['tone'][1] }});">
                                    {{ $L['init'] }}
                                </span>
                                <div class="min-w-0">
                                    <h4 class="font-display text-2xl text-white leading-tight">{{ $L['name'] }}</h4>
                                    <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-white/65 mt-1.5">{{ $L['role'] }}</p>
                                    <p class="text-white/75 italic mt-3 leading-snug">"{{ $L['motto'] }}"</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SVG hierarchy line (draws on scroll) --}}
                    <div class="relative h-16 my-2" aria-hidden="true">
                        <svg viewBox="0 0 400 64" preserveAspectRatio="none" class="absolute inset-0 w-full h-full overflow-visible">
                            <defs>
                                <linearGradient id="orgLineGrad" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#5c7cf5"/>
                                    <stop offset="100%" stop-color="#c8459b"/>
                                </linearGradient>
                            </defs>
                            {{-- Path: top center → split → two ends --}}
                            <path d="M 200 0 L 200 28 L 100 28 L 100 64 M 200 28 L 300 28 L 300 64"
                                  fill="none" stroke="url(#orgLineGrad)" stroke-width="1.5" stroke-linecap="round"
                                  data-org-line />
                        </svg>
                    </div>

                    {{-- REPORT CARDS (2 columns) --}}
                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach ($engineering['team'] as $i => $m)
                            <div class="relative" data-stagger-item style="animation-delay: {{ ($i + 1) * 80 }}ms;">
                                <div class="relative rounded-2xl overflow-hidden border border-white/10 bg-[#160828]/70 backdrop-blur-xl p-5 group/card crew-card h-full" data-tilt>
                                    <div aria-hidden="true" class="absolute inset-0 plate-grid opacity-15"></div>
                                    <div aria-hidden="true" class="absolute -top-10 -right-10 size-32 rounded-full blur-3xl opacity-30 transition-opacity duration-500 group-hover/card:opacity-60"
                                         style="background: radial-gradient(circle, {{ $m['tone'][0] }}, transparent 70%);"></div>

                                    <div class="relative">
                                        <span class="grid place-items-center size-14 rounded-xl text-white font-display font-bold text-lg shadow-lg mb-4"
                                              style="background: linear-gradient(135deg, {{ $m['tone'][0] }}, {{ $m['tone'][1] }});">
                                            {{ $m['init'] }}
                                        </span>
                                        <h4 class="font-display text-xl text-white leading-tight">{{ $m['name'] }}</h4>
                                        <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-white/60 mt-1.5">{{ $m['role'] }}</p>
                                        <p class="text-white/70 italic text-sm mt-3 leading-snug">"{{ $m['motto'] }}"</p>

                                        <div class="mt-5 pt-4 border-t border-white/8 flex items-center justify-between text-[10px] font-mono uppercase tracking-[0.18em] text-white/45">
                                            <span>Reports to {{ $L['init'] }}</span>
                                            <span class="flex items-center gap-1.5">
                                                <span class="size-1 rounded-full bg-emerald-400"></span> Active
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>

                {{-- ── STUDIO + GROWTH columns (flat pods) ── --}}
                <div class="lg:col-span-6 grid gap-6 lg:gap-8 content-start">
                    @foreach ([$studio, $growth] as $dept)
                        <article class="relative rounded-3xl bg-white/[0.03] border border-white/10 p-6 lg:p-8 backdrop-blur-sm" data-reveal>
                            <header class="flex items-start justify-between mb-6 pb-5 border-b border-white/10">
                                <div>
                                    <p class="font-mono text-[10px] uppercase tracking-[0.22em] text-blush-300 mb-2">Dept · {{ $dept['code'] }} · 0{{ count($dept['people']) }} people</p>
                                    <h3 class="font-display text-2xl lg:text-3xl text-white">{{ $dept['name'] }}</h3>
                                    <p class="text-white/55 text-sm mt-1.5">{{ $dept['tag'] }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-400/15 text-emerald-300 text-[10px] font-mono uppercase tracking-[0.18em]">
                                    <span class="size-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    Flat pod
                                </span>
                            </header>

                            <div class="grid sm:grid-cols-2 gap-4">
                                @foreach ($dept['people'] as $i => $m)
                                    <div class="relative" data-stagger-item style="animation-delay: {{ $i * 80 }}ms;">
                                        <div class="relative rounded-2xl overflow-hidden border border-white/10 bg-[#160828]/70 backdrop-blur-xl p-5 group/card crew-card h-full" data-tilt>
                                            <div aria-hidden="true" class="absolute inset-0 plate-grid opacity-15"></div>
                                            <div aria-hidden="true" class="absolute -top-10 -right-10 size-32 rounded-full blur-3xl opacity-30 transition-opacity duration-500 group-hover/card:opacity-60"
                                                 style="background: radial-gradient(circle, {{ $m['tone'][0] }}, transparent 70%);"></div>

                                            <div class="relative">
                                                <span class="grid place-items-center size-14 rounded-xl text-white font-display font-bold text-lg shadow-lg mb-4"
                                                      style="background: linear-gradient(135deg, {{ $m['tone'][0] }}, {{ $m['tone'][1] }});">
                                                    {{ $m['init'] }}
                                                </span>
                                                <h4 class="font-display text-xl text-white leading-tight">{{ $m['name'] }}</h4>
                                                <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-white/60 mt-1.5">{{ $m['role'] }}</p>
                                                <p class="text-white/70 italic text-sm mt-3 leading-snug">"{{ $m['motto'] }}"</p>

                                                <div class="mt-5 pt-4 border-t border-white/8 flex items-center justify-between text-[10px] font-mono uppercase tracking-[0.18em] text-white/45">
                                                    <span>{{ $dept['code'] }} · pod</span>
                                                    <span class="flex items-center gap-1.5">
                                                        <span class="size-1 rounded-full bg-emerald-400"></span> Active
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            {{-- Crew footer note --}}
            <div class="mt-12 grid sm:grid-cols-[1fr_auto] items-center gap-6 pt-8 border-t border-white/10">
                <p class="text-white/55 text-sm leading-relaxed max-w-xl">
                    Every project we ship passes through this room. No outsourced contractors-of-the-week, no middle-management — the same names own design, code, motion and growth from kick-off to maintenance.
                </p>
                <a href="{{ url('/#contact') }}" class="inline-flex items-center gap-2 text-sm font-mono uppercase tracking-[0.18em] text-white hover:text-primary-300 transition-colors">
                    Hire the room
                    <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════════════════
         § 03 · FEATURED SPECIALISTS — sticky picker + editorial profile
         A high-end dossier interface: tap a member on the left, their full
         premium profile spread slides into focus on the right.
         ──────────────────────────────────────────────────────────────────── --}}
    <section id="specialists"
             class="relative bg-mesh-light py-24 lg:py-32 overflow-hidden grain"
             x-data="{ active: 0, total: {{ count($specialists) }} }">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 -right-32 size-[28rem] rounded-full bg-blush-300/30 blur-[120px]"></div>
            <div class="absolute bottom-0 -left-32 size-[24rem] rounded-full bg-primary-300/30 blur-[120px]"></div>
        </div>

        <div class="container-vw relative">
            {{-- Section masthead --}}
            <div class="flex items-center gap-4 mb-10" data-reveal>
                <span class="font-mono text-[10px] uppercase tracking-[0.22em] text-on-surface-muted whitespace-nowrap">Featured Specialists</span>
                <span class="h-px flex-1 bg-on-surface/15"></span>
                <span class="font-mono text-[10px] uppercase tracking-[0.22em] text-on-surface-muted whitespace-nowrap" x-text="`Profile 0${active + 1} / 0${total}`">Profile 01 / 07</span>
            </div>

            <div class="flex items-end justify-between mb-12 flex-wrap gap-6">
                <h2 class="font-display text-[clamp(2.5rem,6vw,5rem)] leading-[0.95] tracking-tight max-w-3xl text-on-surface" data-reveal>
                    Meet the people<br/>
                    <span class="italic font-medium text-on-surface-muted">behind the brands.</span>
                </h2>
                <p class="text-on-surface-muted max-w-sm leading-relaxed" data-reveal data-reveal-delay="0.08">
                    Tap a name on the left. Every specialist below has shipped to production for at least three of the brands we direct.
                </p>
            </div>

            {{-- ── Picker + Profile grid ─────────────────────────────── --}}
            <div class="grid min-w-0 lg:grid-cols-[280px_minmax(0,1fr)] gap-6 lg:gap-10">

                {{-- LEFT · Sticky member picker --}}
                <aside class="min-w-0 lg:sticky lg:top-28 lg:self-start" data-reveal>
                    {{-- Mobile horizontal scroll, Desktop vertical stack --}}
                    <div class="flex w-full max-w-full lg:flex-col gap-2 overflow-x-auto lg:overflow-visible pb-3 lg:pb-0 snap-x snap-mandatory lg:snap-none [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                        @foreach ($specialists as $i => $s)
                            <button type="button"
                                    @click="active = {{ $i }}"
                                    :class="active === {{ $i }} ? 'border-on-surface bg-on-surface text-white' : 'border-on-surface/15 bg-white/60 text-on-surface hover:border-on-surface/35'"
                                    class="group relative flex items-center gap-3 px-3 py-2.5 rounded-2xl border backdrop-blur-sm transition-all duration-300 shrink-0 lg:shrink w-[calc(100%-2rem)] sm:w-auto min-w-0 lg:w-full snap-start text-left">
                                {{-- Avatar --}}
                                <span class="grid place-items-center size-10 rounded-xl text-white font-display font-bold text-sm shadow-md shrink-0"
                                      style="background: linear-gradient(135deg, {{ $s['tone'][0] }}, {{ $s['tone'][1] }});">
                                    {{ $s['init'] }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="font-display text-sm leading-tight truncate">{{ $s['name'] }}</p>
                                    <p class="font-mono text-[9px] uppercase tracking-[0.18em] mt-0.5 opacity-70 truncate">{{ $s['code'] }} · {{ $s['first'] === 'Mubashar' ? 'Lead' : $s['dept'] }}</p>
                                </div>
                                {{-- Active indicator --}}
                                <span class="font-mono text-[9px] opacity-60" x-show="active === {{ $i }}" x-cloak>●</span>
                                <span class="font-mono text-[9px] opacity-40" x-show="active !== {{ $i }}" x-cloak>0{{ $i + 1 }}</span>
                            </button>
                        @endforeach
                    </div>

                    {{-- Keyboard hint --}}
                    <p class="hidden lg:block mt-5 pt-5 border-t border-on-surface/10 font-mono text-[10px] uppercase tracking-[0.18em] text-on-surface-muted">
                        ↑ ↓ to browse · click to pin
                    </p>
                </aside>

                {{-- RIGHT · Profile detail --}}
                <div class="relative min-w-0 min-h-[36rem]">
                    @foreach ($specialists as $i => $s)
                        <article x-show="active === {{ $i }}"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-500"
                                 x-transition:enter-start="opacity-0 translate-y-4"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="relative rounded-3xl overflow-hidden border border-on-surface/10 bg-white shadow-[0_30px_80px_-30px_rgba(15,15,32,0.35)]">
                            <div class="grid min-w-0 md:grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)]">
                                {{-- Portrait pane --}}
                                <div class="relative h-72 sm:h-80 md:h-auto md:min-h-[34rem] overflow-hidden"
                                     style="background: linear-gradient(160deg, {{ $s['tone'][0] }}, {{ $s['tone'][1] }});">
                                    <div aria-hidden="true" class="absolute inset-0 plate-grid opacity-20 mix-blend-overlay"></div>
                                    <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>

                                    {{-- Top meta strip --}}
                                    <div class="absolute top-5 left-5 right-5 flex items-center justify-between text-[9px] font-mono uppercase tracking-[0.22em] text-white/85">
                                        <span class="px-2 py-0.5 rounded-full bg-white/15 backdrop-blur-sm">Specialist · {{ $s['code'] }}</span>
                                        <span>0{{ $i + 1 }} / 0{{ count($specialists) }}</span>
                                    </div>

                                    {{-- Lead crown (only for Mubashar) --}}
                                    @if (isset($s['badge']))
                                        <div class="absolute top-16 left-5">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/95 text-on-surface text-[9px] font-mono uppercase tracking-[0.22em] shadow-lg">
                                                ★ {{ $s['badge'] }}
                                            </span>
                                        </div>
                                    @endif

                                    {{-- Initial monogram (swap to <img> later) --}}
                                    <div class="absolute inset-0 grid place-items-center">
                                        <span class="font-display font-bold text-[7rem] sm:text-[9rem] md:text-[14rem] leading-none text-white/95 select-none">{{ $s['init'] }}</span>
                                    </div>

                                    {{-- Bottom signature --}}
                                    <div class="absolute bottom-0 inset-x-0 px-5 pb-4 pt-12 bg-gradient-to-t from-black/55 to-transparent text-white">
                                        <div class="flex items-end justify-between gap-3">
                                            <div>
                                                <p class="font-mono text-[9px] uppercase tracking-[0.22em] text-white/65">Department</p>
                                                <p class="font-display text-lg leading-none mt-1">{{ $s['dept'] }}</p>
                                            </div>
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-400/20 text-emerald-200 text-[9px] font-mono uppercase tracking-[0.22em]">
                                                <span class="size-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                                On floor
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Editorial detail pane --}}
                                <div class="min-w-0 p-5 sm:p-7 lg:p-10 flex flex-col">
                                    <p class="font-mono text-[10px] uppercase tracking-[0.22em] text-primary-700 mb-3">Profile · 0{{ $i + 1 }}</p>

                                    <h3 class="font-display text-[clamp(2rem,4vw,3.25rem)] leading-[0.95] tracking-tight text-on-surface">
                                        <span class="bg-gradient-to-r bg-clip-text text-transparent" style="background-image: linear-gradient(90deg, {{ $s['tone'][0] }}, {{ $s['tone'][1] }});">{{ $s['first'] }}</span>
                                        @if ($s['name'] !== $s['first'])
                                            <span class="block font-medium italic text-on-surface-muted">{{ trim(str_replace($s['first'], '', $s['name'])) }}</span>
                                        @endif
                                    </h3>

                                    <p class="mt-4 inline-flex max-w-full items-center gap-2 px-3 py-1.5 rounded-2xl sm:rounded-full bg-on-surface text-white text-[10px] font-mono uppercase tracking-[0.14em] sm:tracking-[0.18em] self-start leading-relaxed">
                                        <span class="size-1.5 rounded-full bg-primary-300 inline-block"></span>
                                        {{ $s['role'] }}
                                    </p>

                                    {{-- Stat row --}}
                                    <div class="mt-6 grid grid-cols-1 min-[390px]:grid-cols-3 gap-2 sm:gap-3">
                                        <div class="rounded-xl border border-on-surface/10 bg-mesh-light/60 px-3 py-3">
                                            <p class="font-display text-2xl text-on-surface leading-none">{{ $s['years'] }}</p>
                                            <p class="font-mono text-[9px] uppercase tracking-[0.18em] text-on-surface-muted mt-1.5">Years exp.</p>
                                        </div>
                                        <div class="rounded-xl border border-on-surface/10 bg-mesh-light/60 px-3 py-3">
                                            <p class="font-display text-2xl text-on-surface leading-none">{{ $s['projects'] }}</p>
                                            <p class="font-mono text-[9px] uppercase tracking-[0.18em] text-on-surface-muted mt-1.5">Projects shipped</p>
                                        </div>
                                        <div class="rounded-xl border border-on-surface/10 bg-mesh-light/60 px-3 py-3">
                                            <p class="font-display text-2xl text-on-surface leading-none">{{ $s['stack_count'] }}</p>
                                            <p class="font-mono text-[9px] uppercase tracking-[0.18em] text-on-surface-muted mt-1.5">Tools in rotation</p>
                                        </div>
                                    </div>

                                    <p class="mt-6 text-on-surface-muted leading-relaxed">
                                        {{ $s['intro'] }}
                                    </p>

                                    {{-- Specialties --}}
                                    <div class="mt-6">
                                        <p class="font-mono text-[10px] uppercase tracking-[0.22em] text-on-surface-muted mb-3">Core specialties</p>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach ($s['specialties'] as $tag)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full border border-on-surface/15 bg-white text-[11px] font-mono uppercase tracking-[0.14em] text-on-surface">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Signature work + quote --}}
                                    <div class="mt-6 grid sm:grid-cols-[auto_1fr] gap-3 items-start pt-5 border-t border-on-surface/10">
                                        <p class="font-mono text-[10px] uppercase tracking-[0.22em] text-on-surface-muted whitespace-nowrap">Signature →</p>
                                        <p class="text-sm text-on-surface leading-relaxed">{{ $s['signature'] }}</p>
                                    </div>

                                    <blockquote class="mt-auto pt-6">
                                        <p class="font-display italic text-xl lg:text-2xl text-on-surface leading-snug">
                                            <span class="bg-gradient-to-r bg-clip-text text-transparent" style="background-image: linear-gradient(90deg, {{ $s['tone'][0] }}, {{ $s['tone'][1] }});">"</span>{{ $s['quote'] }}<span class="bg-gradient-to-r bg-clip-text text-transparent" style="background-image: linear-gradient(90deg, {{ $s['tone'][0] }}, {{ $s['tone'][1] }});">"</span>
                                        </p>
                                        <p class="font-mono text-[10px] uppercase tracking-[0.22em] text-on-surface-muted mt-3">— {{ $s['first'] }}, {{ $s['dept'] }}</p>
                                    </blockquote>
                                </div>
                            </div>
                        </article>
                    @endforeach

                    {{-- Pager (prev / next) --}}
                    <div class="flex items-center justify-between mt-6">
                        <button type="button"
                                @click="active = (active - 1 + total) % total"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-full border border-on-surface/15 bg-white hover:border-on-surface/35 transition-colors font-mono text-[10px] uppercase tracking-[0.18em] text-on-surface">
                            <span aria-hidden="true">←</span> Previous
                        </button>
                        <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-on-surface-muted" x-text="`${active + 1} of ${total}`">1 of 7</p>
                        <button type="button"
                                @click="active = (active + 1) % total"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-full border border-on-surface/15 bg-white hover:border-on-surface/35 transition-colors font-mono text-[10px] uppercase tracking-[0.18em] text-on-surface">
                            Next <span aria-hidden="true">→</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════════════════
         § 04 · BRANDS MARQUEE — the 8 brands we direct (scrolling rail)
         ──────────────────────────────────────────────────────────────────── --}}
    <section class="relative bg-mesh-light py-20 lg:py-24 overflow-hidden">
        <div class="container-vw mb-10">
            <div class="flex items-center gap-4" data-reveal>
                <span class="font-mono text-[10px] uppercase tracking-[0.22em] text-on-surface-muted whitespace-nowrap">Active Directions</span>
                <span class="h-px flex-1 bg-on-surface/15"></span>
                <span class="font-mono text-[10px] uppercase tracking-[0.22em] text-on-surface-muted whitespace-nowrap">08 brands</span>
            </div>
        </div>

        {{-- Edge-fade marquee --}}
        <div class="relative" aria-hidden="true">
            <div class="absolute inset-y-0 left-0 w-32 bg-gradient-to-r from-surface to-transparent z-10 pointer-events-none"></div>
            <div class="absolute inset-y-0 right-0 w-32 bg-gradient-to-l from-surface to-transparent z-10 pointer-events-none"></div>
            <div class="marquee-track flex gap-12 font-display text-[clamp(2.5rem,5vw,4.5rem)] leading-none whitespace-nowrap text-on-surface/85">
                @for ($i = 0; $i < 2; $i++)
                    @foreach ($founder['brands'] as $b)
                        <span class="flex items-center gap-12">
                            {{ $b }}
                            <span class="inline-block size-3 rounded-full bg-gradient-to-br from-primary-500 to-blush-500"></span>
                        </span>
                    @endforeach
                @endfor
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════════════════
         § 05 · TIMELINE — A decade in six chapters (scroll-driven spine)
         ──────────────────────────────────────────────────────────────────── --}}
    <section class="relative bg-brand-stage text-white py-24 lg:py-32 overflow-hidden grain">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute top-1/3 -left-32 size-[26rem] rounded-full bg-primary-700/25 blur-3xl"></div>
            <div class="absolute bottom-1/4 -right-24 size-[22rem] rounded-full bg-blush-500/15 blur-3xl"></div>
        </div>

        <div class="container-vw relative max-w-4xl">
            <div class="flex items-center gap-4 mb-10" data-reveal>
                <span class="font-mono text-[10px] uppercase tracking-[0.22em] text-white/55 whitespace-nowrap">The Timeline</span>
                <span class="h-px flex-1 bg-white/15"></span>
                <span class="font-mono text-[10px] uppercase tracking-[0.22em] text-white/55 whitespace-nowrap">10 years · 06 chapters</span>
            </div>

            <h2 class="font-display text-[clamp(2.25rem,5vw,4rem)] leading-[0.98] tracking-tight mb-16 max-w-2xl" data-reveal data-reveal-delay="0.05">
                A decade.<br/>
                <span class="italic font-medium text-white/65">Six chapters worth telling.</span>
            </h2>

            {{-- Vertical thread with scroll-driven progress --}}
            <div class="relative" data-timeline>
                {{-- Spine + filled progress --}}
                <span aria-hidden="true" class="absolute left-[7px] top-2 bottom-2 w-px bg-white/10"></span>
                <span aria-hidden="true" class="absolute left-[7px] top-2 w-px bg-gradient-to-b from-primary-400 via-blush-400 to-primary-700 origin-top scale-y-0" data-timeline-progress></span>

                <ol class="space-y-12 lg:space-y-14">
                    @foreach ($timeline as $t)
                        <li class="relative pl-10" data-stagger-item>
                            <span aria-hidden="true" class="absolute left-0 top-1.5 size-4 rounded-full border-2 border-primary-400 bg-surface-ink"></span>
                            <div class="flex items-baseline gap-4 mb-2">
                                <span class="font-mono text-[10px] uppercase tracking-[0.22em] text-primary-300">{{ $t['year'] }}</span>
                                <span class="h-px flex-1 bg-white/10"></span>
                            </div>
                            <h3 class="font-display text-2xl lg:text-3xl text-white leading-tight mb-2">{{ $t['title'] }}</h3>
                            <p class="text-white/65 leading-relaxed max-w-xl">{{ $t['note'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════════════════
         § 06 · ABOUT FAQ + KEY TAKEAWAYS (AEO — SEO rewrite, 2026)
         ──────────────────────────────────────────────────────────────────── --}}
    @php
        $aboutFaqs = [
            [
                'q' => 'Who is Junaid Swati?',
                'a' => 'Junaid Swati is the founder of Vowlyn, leading delivery and stakeholder strategy across every engagement. Over a decade he has helped build and direct eight brands.',
            ],
            [
                'q' => 'How big is the Vowlyn team?',
                'a' => 'Eight specialists across three in-house pods — engineering, studio, and growth — with no outsourced contractors and no middle-management layer.',
            ],
            [
                'q' => 'Is Vowlyn a real studio with a real team?',
                'a' => 'Yes. Vowlyn is a focused room of eight named specialists who own design, code, motion, and growth for every brand it directs, from kickoff to maintenance.',
            ],
            [
                'q' => 'Where is Vowlyn based and who does it serve?',
                'a' => 'Vowlyn began serving local Burlington brands and now directs work across North America, Europe, and the MENA region.',
            ],
        ];
    @endphp
    <section aria-labelledby="paa-about" class="section-vw bg-surface relative overflow-hidden">
        <div class="container-vw relative">
            <div class="grid lg:grid-cols-[0.8fr_1.2fr] gap-10 lg:gap-16 items-start">
                <div data-reveal>
                    <span class="eyebrow !text-primary-700">People also ask</span>
                    <h2 id="paa-about" class="headline-display text-3xl lg:text-4xl mt-3 text-slate-900">
                        About Vowlyn — common questions.
                    </h2>
                    <p class="text-on-surface-muted mt-4 leading-relaxed">
                        The entity answers, plainly — who runs the studio, how big it is, and where it works.
                    </p>

                    <x-key-takeaways class="mt-8" :items="[
                        'Vowlyn is an eight-person software development studio founded and led by Junaid Swati.',
                        'Three in-house pods: engineering, studio, and growth — no outsourced contractors.',
                        '10+ years directing eight brands, from local services to multi-city buying groups.',
                        'The same team owns design, code, motion, and growth end-to-end.',
                    ]" />
                </div>

                <x-faq-accordion :items="$aboutFaqs" />
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════════════════
         § 07 · CLOSING CTA
         ──────────────────────────────────────────────────────────────────── --}}
    <section class="relative bg-surface py-24 lg:py-32 overflow-hidden">
        <div class="container-vw max-w-4xl text-center">
            <p class="eyebrow !text-primary-700 mb-5" data-reveal>One last thing</p>
            <h2 class="font-display text-[clamp(2.25rem,5vw,4rem)] leading-[0.98] tracking-tight text-on-surface" data-reveal data-reveal-delay="0.05">
                If you've read this far, you should<br/>
                <span class="bg-gradient-to-r from-primary-500 via-blush-500 to-primary-700 bg-clip-text text-transparent">probably talk to Junaid.</span>
            </h2>
            <p class="mt-8 text-lg text-on-surface-muted max-w-xl mx-auto leading-relaxed" data-reveal data-reveal-delay="0.1">
                A 30-minute call. No pitch deck. We'll tell you whether we're the right room for what you're building.
            </p>
            <div class="mt-10 flex items-center justify-center gap-4 flex-wrap" data-reveal data-reveal-delay="0.15">
                <a href="https://calendly.com/junaidswati/new-meeting" target="_blank" rel="noopener noreferrer" class="btn-primary">Book a 30-min discovery</a>
                <a href="{{ route('portfolio') }}" class="inline-flex items-center gap-2 text-sm font-mono uppercase tracking-[0.18em] text-on-surface hover:text-primary-700 transition-colors">
                    See what we've shipped →
                </a>
            </div>
        </div>
    </section>
@endsection
