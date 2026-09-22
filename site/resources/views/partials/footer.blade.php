{{-- ============================================================================
FOOTER — Dark stage with logo (white wordmark), nav, contact, socials
============================================================================ --}}
<footer class="bg-brand-stage text-on-inverse relative overflow-hidden grain">
    {{-- Decorative gradient blob --}}
    <div aria-hidden="true"
        class="absolute -top-40 -left-20 size-[28rem] rounded-full bg-primary-700/30 blur-3xl pointer-events-none">
    </div>
    <div aria-hidden="true"
        class="absolute -bottom-32 right-0 size-[24rem] rounded-full bg-blush-500/15 blur-3xl pointer-events-none">
    </div>

    <div class="container-vw relative">
        {{-- Top: Big CTA band --}}
        <div class="py-20 lg:py-28 border-b border-white/8" data-reveal>
            <div class="grid lg:grid-cols-[1.4fr_1fr] gap-10 items-end">
                <h2 class="headline-display text-5xl sm:text-6xl lg:text-7xl text-white">
                    Let's build something
                    <span class="text-brand-gradient">worth scaling.</span>
                </h2>
                <div class="space-y-5">
                    <p class="text-white/65 text-lg max-w-md">
                        Share your goals — we'll return with a strategic action plan, timeline, and product roadmap
                        within 24 hours.
                    </p>
                    <a href="{{ url('/#contact') }}" data-magnetic="0.2" class="btn btn-ghost-light !py-3.5 !px-7">
                        Start a project
                        <i data-lucide="arrow-up-right" class="size-4"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Mid: Link columns --}}
        <div class="py-14 grid grid-cols-2 md:grid-cols-4 gap-10">
            <div class="col-span-2 md:col-span-1 space-y-4">
                <img src="{{ asset('brand/vowlyn-logo.png') }}" alt="Vowlyn" class="h-12 w-auto" />
                <p class="text-white/55 text-sm leading-relaxed max-w-xs">
                    Premium software company for modern digital growth.
                </p>
            </div>

            <div>
                <p class="text-white/45 text-xs font-mono uppercase tracking-[0.16em] mb-4">Services</p>
                <ul class="space-y-2.5 text-sm">
                    @foreach (['Modern Web Apps', 'Mobile Engineering', 'AI Integration', 'Scalable SaaS', 'Enterprise Security', 'Cloud DevOps'] as $s)
                        <li><a href="{{ route('services') }}" class="text-white/75 hover:text-white transition">{{ $s }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <p class="text-white/45 text-xs font-mono uppercase tracking-[0.16em] mb-4">Company</p>
                <ul class="space-y-2.5 text-sm">
                    @foreach ([
                            ['About', route('about')],
                            ['Process', url('/#process')],
                            ['Portfolio', route('portfolio')],
                            ['Content Creation', route('content-creation')],
                            ['Marketing', route('performance-marketing')],
                            ['Journal', route('blog.index')],
                            ['Why Us', route('why-us')],
                            ['Testimonials', url('/#testimonials')],
                            ['Contact', url('/#contact')],
                        ] as [$l, $h])
                            <li><a href="{{ $h }}" class="text-white/75 hover:text-white transition">{{ $l }}</a></li>
                    @endforeach

                                               </ul>
        
                                   </div>
             
                  
            <div>
                <p class="text-white/45 text-xs font-mono uppercase tracking-[0.16em] mb-4">Connect</p>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#" class="text-white/75 hover:text-white transition inline-flex items-center gap-2"><span aria-hidden="true" class="grid size-4 place-items-center text-[0.6rem] font-bold leading-none">in</span> LinkedIn</a></li>
                    <li><a href="#" class="text-white/75 hover:text-white transition inline-flex items-center gap-2"><span aria-hidden="true" class="grid size-4 place-items-center text-xs leading-none">●</span> Dribbble</a></li>
                    <li><a href="#" class="text-white/75 hover:text-white transition inline-flex items-center gap-2"><i data-lucide="x" class="size-4"></i> X / Twitter</a></li>
            
               </ul>
                <p class="text-white/45 text-xs font-mono uppercase tracking-[0.16em] mt-8 mb-3">Regions</p>
                <p class="text-white/65 text-sm">North America · Europe · MENA</p>
            </div>
        </div>

        {{-- Bottom: legal strip --}}
        <div class="py-6 border-t border-white/8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-white/45">
            <p>&copy; {{ date('Y') }} Vowlyn. All rights reserved.</p>
            <p class="font-mono uppercase tracking-[0.16em]">Premium · Built to scale</p>
        </div>
    </div>
</footer>
