@php($heroServiceOptions = \App\Support\ServiceCatalog::options())

<div id="hero-contact-form" class="relative min-w-0 scroll-mt-24 sm:scroll-mt-28 lg:pl-2" data-reveal data-reveal-delay="0.14">
    {{-- Atmospheric frame keeps the form integrated with the hero artwork language. --}}
    <div aria-hidden="true" class="absolute -inset-8 pointer-events-none">
        <div class="absolute inset-x-10 top-12 h-4/5 rounded-full bg-primary-300/20 blur-3xl"></div>
        <div class="absolute -right-4 -top-4 size-24 rounded-full border border-primary-300/35"></div>
        <div class="absolute -right-1 top-10 size-3 rounded-full bg-blush-400 shadow-[0_0_24px_rgba(244,96,127,.65)]"></div>
        <div class="absolute -left-5 bottom-14 size-16 rounded-2xl bg-gradient-to-br from-primary-300/35 to-blush-300/35 rotate-12 blur-[1px]"></div>
    </div>

    <form action="{{ route('contact.store') }}" method="POST"
          class="relative w-full min-w-0 overflow-hidden rounded-[2rem] border border-white/75 bg-white/82 p-5 shadow-[0_32px_80px_-24px_rgba(75,0,130,.28)] backdrop-blur-2xl sm:p-7 lg:p-8"
          x-data="{ loading: false }"
          @submit="loading = true">
        @csrf
        <input type="hidden" name="form_source" value="hero" />

        <div aria-hidden="true" class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-primary-400 to-transparent"></div>
        <div aria-hidden="true" class="absolute -right-16 -top-16 size-48 rounded-full bg-blush-200/35 blur-3xl"></div>

        <div class="relative mb-6 flex items-start justify-between gap-5 border-b border-primary-700/8 pb-5">
            <div>
                <div class="mb-2 flex items-center gap-2">
                    <span class="relative grid size-2 place-items-center">
                        <span class="absolute inset-0 animate-ping rounded-full bg-emerald-400 opacity-60"></span>
                        <span class="relative size-2 rounded-full bg-emerald-500"></span>
                    </span>
                    <span class="font-mono text-[10px] uppercase tracking-[0.18em] text-on-surface/50">Taking on new projects</span>
                </div>
                <h2 class="font-display text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Tell us what you’re building.</h2>
                <p class="mt-1.5 text-sm leading-relaxed text-on-surface/55">A few details now. A useful plan within 24 hours.</p>
            </div>
            <span class="hidden size-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-primary-100 to-blush-100 text-primary-700 ring-1 ring-primary-700/10 sm:grid">
                <i data-lucide="send" class="size-5"></i>
            </span>
        </div>

        @if (session('contact.success'))
            <div class="relative mb-5 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800" role="status">
                <i data-lucide="check-circle-2" class="size-5 shrink-0"></i>
                <p class="text-sm">{{ session('contact.success') }}</p>
            </div>
        @endif

        <div class="relative grid gap-4 sm:grid-cols-2">
            <div>
                <label for="hero-name" class="eyebrow !mb-2 block !text-on-surface/55">Name *</label>
                <input type="text" id="hero-name" name="name" required autocomplete="name" value="{{ old('name') }}"
                       class="w-full rounded-xl border-2 border-transparent bg-lavender-100/75 px-4 py-3.5 text-slate-900 outline-none transition placeholder:text-on-surface/35 hover:bg-lavender-100 focus:border-primary-700 focus:bg-white"
                       placeholder="Your full name" />
                @error('name') <p class="mt-1.5 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="hero-email" class="eyebrow !mb-2 block !text-on-surface/55">Email *</label>
                <input type="email" id="hero-email" name="email" required autocomplete="email" value="{{ old('email') }}"
                       class="w-full rounded-xl border-2 border-transparent bg-lavender-100/75 px-4 py-3.5 text-slate-900 outline-none transition placeholder:text-on-surface/35 hover:bg-lavender-100 focus:border-primary-700 focus:bg-white"
                       placeholder="you@company.com" />
                @error('email') <p class="mt-1.5 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="hero-service" class="eyebrow !mb-2 block !text-on-surface/55">What do you need? *</label>
                <div class="relative">
                    <select id="hero-service" name="service" required
                            class="w-full appearance-none rounded-xl border-2 border-transparent bg-lavender-100/75 px-4 py-3.5 pr-11 text-slate-900 outline-none transition hover:bg-lavender-100 focus:border-primary-700 focus:bg-white">
                        <option value="" disabled @selected(!old('service'))>Choose a Vowlyn service</option>
                        @foreach ($heroServiceOptions as $slug => $service)
                            <option value="{{ $slug }}" @selected(old('service') === $slug)>{{ $service }}</option>
                        @endforeach
                    </select>
                    <i data-lucide="chevrons-up-down" class="pointer-events-none absolute right-4 top-1/2 size-4 -translate-y-1/2 text-primary-700"></i>
                </div>
                @error('service') <p class="mt-1.5 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="hero-brief" class="eyebrow !mb-2 block !text-on-surface/55">Project brief *</label>
                <textarea id="hero-brief" name="brief" rows="4" required
                          class="w-full resize-none rounded-xl border-2 border-transparent bg-lavender-100/75 px-4 py-3.5 text-slate-900 outline-none transition placeholder:text-on-surface/35 hover:bg-lavender-100 focus:border-primary-700 focus:bg-white"
                          placeholder="Goals, timeline, and what success looks like…">{{ old('brief') }}</textarea>
                @error('brief') <p class="mt-1.5 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            {{-- Honeypot: hidden from people, validated empty on the server. --}}
            <div class="hidden" aria-hidden="true">
                <label>Website (leave blank)<input type="text" name="website" tabindex="-1" autocomplete="off" /></label>
            </div>
        </div>

        <div class="relative mt-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="flex items-center gap-2 text-xs leading-relaxed text-on-surface/45">
                <i data-lucide="lock-keyhole" class="size-3.5 shrink-0 text-primary-700"></i>
                Private, secure, and never shared.
            </p>
            <button type="submit" :disabled="loading"
                    class="btn btn-primary min-h-12 w-full justify-center !px-6 !py-3.5 disabled:cursor-wait disabled:opacity-70 sm:w-auto">
                <span x-show="!loading">Send Request</span>
                <span x-show="loading" x-cloak>Sending…</span>
                <i data-lucide="arrow-up-right" class="size-4" x-show="!loading"></i>
                <i data-lucide="loader-2" class="size-4 animate-spin" x-show="loading" x-cloak></i>
            </button>
        </div>
    </form>

    <div class="relative mx-auto mt-4 flex w-full flex-wrap items-center justify-center gap-x-4 gap-y-2 rounded-2xl border border-primary-700/8 bg-white/55 px-3 py-2.5 text-[9px] font-mono uppercase tracking-[0.1em] text-on-surface/45 backdrop-blur-xl sm:w-[calc(100%-2rem)] sm:flex-nowrap sm:gap-8 sm:rounded-full sm:px-4 sm:text-[10px] sm:tracking-[0.12em]">
        <span class="inline-flex items-center gap-1.5"><i data-lucide="clock-3" class="size-3.5 text-primary-700"></i>24h response</span>
        <span class="hidden h-3 w-px bg-primary-700/12 sm:block"></span>
        <span class="inline-flex items-center gap-1.5"><i data-lucide="code-2" class="size-3.5 text-primary-700"></i>You own the code</span>
    </div>
</div>
