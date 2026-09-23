{{-- ============================================================================
   SERVICES MENU — desktop hover panel
   Structure follows the client's reference (full-width discipline grid with
   ruled rows, then a callout band that leads into "Get in touch"), rendered in
   Vowlyn's own idiom: deep ink, brand-gradient hairlines, mono labels.

   The disciplines come from ServiceCatalog so this menu and the /services page
   can never drift apart. Open/close is CSS-only (see app.css) — app.js only
   mirrors the state onto aria-expanded and lets Escape dismiss it.
   ============================================================================ --}}
@php($services = \App\Support\ServiceCatalog::list())

<div data-services-panel class="absolute inset-x-0 top-full pt-3">
    <div class="mx-auto max-w-7xl">
        <div class="services-menu__card relative overflow-hidden rounded-[1.75rem] border border-white/10 bg-surface-ink/98 shadow-[0_48px_90px_-40px_rgba(13,4,32,.95)] backdrop-blur-2xl">

            {{-- Brand gradient hairline along the top edge --}}
            <span aria-hidden="true" class="absolute inset-x-0 top-0 h-px bg-[linear-gradient(90deg,transparent,#18d2ff_14%,#7b41b3_48%,#c8459b_84%,transparent)]"></span>
            <span aria-hidden="true" class="plate-grid pointer-events-none absolute inset-0 opacity-[0.05]"></span>

            <div class="relative">

                {{-- ── Disciplines ─────────────────────────────────────────── --}}
                <div class="px-7 pt-6 pb-2 lg:px-10 lg:pt-8">
                    <div class="flex items-baseline justify-between gap-6 border-b border-white/10 pb-3">
                        <p class="font-mono text-[10px] uppercase tracking-[.22em] text-white/55">Disciplines</p>
                        <p class="font-mono text-[10px] uppercase tracking-[.22em] text-white/40">01 — 06</p>
                    </div>

                    <ul class="grid gap-x-10 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($services as $service)
                            <li class="services-menu__row" style="--row: {{ $loop->index }}">
                                <a href="{{ route('services.show', $service['slug']) }}"
                                   class="group/svc relative flex items-center gap-3.5 py-4 lg:py-[1.15rem]">
                                    <span class="font-mono text-[10px] leading-none tracking-[.12em] text-white/45 transition-colors duration-300 group-hover/svc:text-[#18d2ff]">{{ $service['no'] }}</span>

                                    <span class="min-w-0 flex-1">
                                        <span class="block font-display text-[0.95rem] font-semibold leading-tight tracking-[-0.01em] text-white/85 transition-colors duration-300 group-hover/svc:text-white">{{ $service['name'] }}</span>
                                        <span class="mt-1 block font-mono text-[9px] uppercase tracking-[.18em] text-white/50 transition-colors duration-300 group-hover/svc:text-white/70">{{ $service['eyebrow'] }}</span>
                                    </span>

                                    {{-- Affordance: slides in from the left on hover --}}
                                    <i data-lucide="arrow-up-right" class="size-4 shrink-0 -translate-x-1.5 text-[#18d2ff] opacity-0 transition duration-300 group-hover/svc:translate-x-0 group-hover/svc:opacity-100"></i>

                                    {{-- The rule under each row doubles as the hover underline --}}
                                    <span aria-hidden="true" class="absolute inset-x-0 bottom-0 h-px bg-white/10"></span>
                                    <span aria-hidden="true" class="absolute inset-x-0 bottom-0 h-px origin-left scale-x-0 bg-[linear-gradient(90deg,#18d2ff,#7b41b3_55%,#c8459b)] transition-transform duration-500 group-hover/svc:scale-x-100"></span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="pb-4 pt-1">
                        <a href="{{ route('services') }}"
                           class="group/all inline-flex items-center gap-2 font-mono text-[10px] uppercase tracking-[.2em] text-primary-300 transition-colors hover:text-white">
                            All services
                            <i data-lucide="arrow-right" class="size-3.5 transition-transform duration-300 group-hover/all:translate-x-1"></i>
                        </a>
                    </div>
                </div>

                {{-- ── Callout band — mirrors the reference's "prefer to talk?" strip ── --}}
                <div class="flex flex-wrap items-center justify-between gap-6 border-t border-white/10 bg-white/[0.045] px-7 py-6 lg:px-10 lg:py-7">
                    <div class="max-w-xl">
                        <p class="font-mono text-[10px] uppercase tracking-[.22em] text-[#18d2ff]">Prefer to talk it through?</p>
                        <p class="mt-2.5 font-display text-lg font-semibold leading-snug tracking-[-0.02em] text-white lg:text-xl">
                            Tell us where the project stands — we'll point you to the right discipline and scope.
                        </p>
                    </div>

                    <div class="flex items-center gap-5">
                        {{-- Dashed gradient arrow, drawn toward the call to action --}}
                        <svg aria-hidden="true" viewBox="0 0 104 26" fill="none" class="services-menu__arrow hidden h-6 w-24 shrink-0 xl:block">
                            <defs>
                                <linearGradient id="services-menu-arrow" x1="0" y1="0" x2="104" y2="0" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#18d2ff" />
                                    <stop offset="0.52" stop-color="#7b41b3" />
                                    <stop offset="1" stop-color="#c8459b" />
                                </linearGradient>
                            </defs>
                            <path d="M1 19C22 19 27 7 48 7c14 0 22 4 35 4" stroke="url(#services-menu-arrow)" stroke-width="1.5" stroke-linecap="round" stroke-dasharray="4 6" class="services-menu__arrow-dashes" />
                            <path d="M83 4.5 95.5 11 83 17.5" stroke="url(#services-menu-arrow)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                        <a href="{{ url('/#contact') }}" data-magnetic="0.16" class="btn btn-primary shrink-0 !px-6">
                            <span>Start a project</span>
                            <i data-lucide="arrow-up-right" class="size-4 -mr-0.5"></i>
                        </a>

                        <a href="https://calendly.com/junaidswati/new-meeting" target="_blank" rel="noopener noreferrer"
                           class="hidden shrink-0 font-mono text-[10px] uppercase tracking-[.2em] text-white/55 transition-colors hover:text-white sm:block">
                            or book a 30-min call
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
