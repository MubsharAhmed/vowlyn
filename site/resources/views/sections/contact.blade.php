{{-- ============================================================================
   CONTACT — "Start Your Project With Vowlyn"
   Split layout: info column + form. CSRF protected, Alpine validation hints.
   ============================================================================ --}}
@php($serviceOptions = \App\Support\ServiceCatalog::options())

<section id="contact" class="section-vw bg-gradient-to-b from-lavender-100/50 via-surface to-surface relative overflow-hidden">
    <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
        <div class="absolute -bottom-32 -left-20 size-[32rem] rounded-full bg-primary-300/25 blur-3xl"></div>
        <div class="absolute -top-20 -right-20 size-[28rem] rounded-full bg-blush-200/40 blur-3xl"></div>
    </div>

    <div class="container-vw relative">
        <div class="grid lg:grid-cols-[1fr_1.15fr] gap-12 lg:gap-20">

            {{-- LEFT: copy + meta --}}
            <div>
                <div class="eyebrow-row mb-5" data-reveal>
                    <span class="eyebrow">Contact</span>
                </div>
                <h2 class="headline-display text-4xl sm:text-5xl lg:text-6xl text-slate-900 mb-7" data-reveal data-reveal-delay="0.05">
                    Start Your Software Project<br />With <span class="text-brand-gradient">Vowlyn.</span>
                </h2>
                <p class="text-lg text-on-surface/65 leading-relaxed mb-10 max-w-md" data-reveal data-reveal-delay="0.12">
                    Share your goals and we'll return with a strategic action plan, timeline, and product roadmap —
                    within 24 hours.
                </p>

                <ul class="space-y-5" data-stagger="0.1">
                    @foreach ([
                        ['timer', 'Response time', 'within 24 hours'],
                        ['handshake', 'Engagement models', 'fixed scope · dedicated team · retainer'],
                        ['globe', 'Regions served', 'North America · Europe · MENA'],
                    ] as [$icon, $title, $value])
                        <li data-stagger-item class="flex items-start gap-4 pb-5 border-b border-lavender-300/60">
                            <div class="grid place-items-center size-11 rounded-2xl bg-white text-primary-700 border border-lavender-300 shrink-0">
                                <i data-lucide="{{ $icon }}" class="size-5"></i>
                            </div>
                            <div>
                                <p class="eyebrow !text-on-surface/45 mb-1">{{ $title }}</p>
                                <p class="text-slate-900 font-medium">{{ $value }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- RIGHT: form --}}
            <div data-reveal data-reveal-delay="0.1">
                <form action="{{ route('contact.store') }}" method="POST"
                      class="card-vw !p-7 sm:!p-10 bg-white/85 backdrop-blur-xl shadow-xl"
                      x-data="{ loading: false }"
                      @submit="loading = true">
                    @csrf
                    <input type="hidden" name="form_source" value="contact" />

                    {{-- Status flash --}}
                    @if (session('contact.success'))
                        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 flex items-center gap-3 text-emerald-800">
                            <i data-lucide="check-circle-2" class="size-5"></i>
                            <p class="text-sm">{{ session('contact.success') }}</p>
                        </div>
                    @endif

                    <div class="grid sm:grid-cols-2 gap-5">
                        {{-- Name --}}
                        <div class="sm:col-span-1">
                            <label for="name" class="eyebrow !text-on-surface/55 mb-2 block">Name *</label>
                            <input type="text" id="name" name="name" required value="{{ old('name') }}"
                                   class="w-full rounded-xl bg-lavender-100/70 focus:bg-white border-2 border-transparent focus:border-primary-700 px-4 py-3.5 text-slate-900 placeholder:text-on-surface/40 transition outline-none"
                                   placeholder="Your full name" />
                            @error('name') <p class="mt-2 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email --}}
                        <div class="sm:col-span-1">
                            <label for="email" class="eyebrow !text-on-surface/55 mb-2 block">Email *</label>
                            <input type="email" id="email" name="email" required value="{{ old('email') }}"
                                   class="w-full rounded-xl bg-lavender-100/70 focus:bg-white border-2 border-transparent focus:border-primary-700 px-4 py-3.5 text-slate-900 placeholder:text-on-surface/40 transition outline-none"
                                   placeholder="you@company.com" />
                            @error('email') <p class="mt-2 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Service --}}
                        <div class="sm:col-span-2">
                            <label for="contact-service" class="eyebrow !text-on-surface/55 mb-2 block">How can we help? *</label>
                            <div class="relative">
                                <select id="contact-service" name="service" required
                                        class="w-full appearance-none rounded-xl bg-lavender-100/70 focus:bg-white border-2 border-transparent focus:border-primary-700 px-4 py-3.5 pr-11 text-slate-900 transition outline-none">
                                    <option value="" disabled @selected(!old('service'))>Choose a service</option>
                                    @foreach ($serviceOptions as $slug => $service)
                                        <option value="{{ $slug }}" @selected(old('service') === $slug)>{{ $service }}</option>
                                    @endforeach
                                </select>
                                <i data-lucide="chevrons-up-down" class="absolute right-4 top-1/2 size-4 -translate-y-1/2 text-primary-700 pointer-events-none"></i>
                            </div>
                            @error('service') <p class="mt-2 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Project brief --}}
                        <div class="sm:col-span-2">
                            <label for="brief" class="eyebrow !text-on-surface/55 mb-2 block">Project Brief *</label>
                            <textarea id="brief" name="brief" rows="5" required
                                      class="w-full rounded-xl bg-lavender-100/70 focus:bg-white border-2 border-transparent focus:border-primary-700 px-4 py-3.5 text-slate-900 placeholder:text-on-surface/40 transition outline-none resize-none"
                                      placeholder="Tell us about your goals, timeline, and what success looks like.">{{ old('brief') }}</textarea>
                            @error('brief') <p class="mt-2 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Honeypot (anti-spam) --}}
                        <div class="hidden" aria-hidden="true">
                            <label>Website (leave blank)<input type="text" name="website" tabindex="-1" autocomplete="off" /></label>
                        </div>
                    </div>

                    <div class="mt-7 flex items-center justify-between gap-4">
                        <p class="text-xs text-on-surface/50 max-w-xs">We respect your privacy. Your details are never shared.</p>
                        <button type="submit"
                                :disabled="loading"
                                class="btn btn-primary !py-4 !px-7 disabled:opacity-70 disabled:cursor-wait">
                            <span x-show="!loading">Send Request</span>
                            <span x-show="loading" x-cloak>Sending…</span>
                            <i data-lucide="arrow-up-right" class="size-4" x-show="!loading"></i>
                            <i data-lucide="loader-2" class="size-4 animate-spin" x-show="loading" x-cloak></i>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</section>
