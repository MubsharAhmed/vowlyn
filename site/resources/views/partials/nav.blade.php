{{-- ============================================================================
   NAVIGATION — Dark glass pill nav (logo wordmark is white → needs dark bg)
   Floating, centered, transforms on scroll. Mobile = full-screen overlay.
   ============================================================================ --}}
<nav
    data-nav
    x-data="{ open: false }"
    class="fixed top-4 inset-x-0 z-40 px-4 transition-all duration-500 [&.is-scrolled>div]:bg-surface-ink/85 [&.is-scrolled>div]:backdrop-blur-xl"
    :class="open && '!top-0 !px-0'"
>
    <div class="mx-auto max-w-7xl pill-dark rounded-full px-4 sm:px-6 py-3 flex items-center justify-between shadow-xl transition-all duration-500"
         :class="open && '!rounded-none !max-w-none !shadow-none'">

        {{-- Logo --}}
        <a href="{{ url('/#home') }}" class="flex items-center gap-2.5 shrink-0 group" aria-label="Vowlyn home">
            <img src="{{ asset('brand/vowlyn-logo.png') }}" alt="Vowlyn" class="h-9 w-auto sm:h-10 select-none" draggable="false" />
        </a>

        {{-- Desktop links --}}
        <ul class="hidden lg:flex items-center gap-1 text-sm font-medium">
            @foreach ([
                ['Services', route('services')],
                ['Portfolio', route('portfolio')],
                ['Marketplace', route('marketplace')],
                ['About', route('about')],
                ['Why Us', url('/#why-us')],
            ] as [$label, $href])
                <li>
                    <a href="{{ $href }}"
                       class="px-3.5 py-2 rounded-full text-on-inverse/75 hover:text-white hover:bg-white/8 transition">
                        {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>

        {{-- Right cluster --}}
        <div class="flex items-center gap-2">
            <a href="{{ url('/#contact') }}"
               data-magnetic="0.18"
               class="btn btn-primary !py-2.5 !px-5 !text-sm hidden sm:inline-flex">
                <span>Start a Project</span>
                <i data-lucide="arrow-up-right" class="size-4 -mr-0.5"></i>
            </a>

            {{-- Mobile toggle --}}
            <button
                @click="open = !open"
                :aria-expanded="open"
                aria-label="Toggle navigation"
                class="lg:hidden grid place-items-center size-10 rounded-full bg-white/8 text-white hover:bg-white/15 transition"
            >
                <i data-lucide="menu" x-show="!open" class="size-5"></i>
                <i data-lucide="x" x-show="open" x-cloak class="size-5"></i>
            </button>
        </div>
    </div>

    {{-- Mobile overlay --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="lg:hidden fixed inset-0 top-[64px] bg-surface-ink/98 backdrop-blur-2xl"
    >
        <ul class="flex flex-col p-6 gap-1 text-on-inverse font-display text-3xl font-medium">
            @foreach ([
                ['Services', route('services')],
                ['Portfolio', route('portfolio')],
                ['Marketplace', route('marketplace')],
                ['About', route('about')],
                ['Why Us', url('/#why-us')],
                ['Contact', url('/#contact')],
            ] as [$label, $href])
                <li>
                    <a href="{{ $href }}"
                       @click="open = false"
                       class="block py-3 border-b border-white/8 hover:text-primary-300 transition">
                        {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="p-6 pt-2">
            <a href="{{ url('/#contact') }}" @click="open = false" class="btn btn-primary w-full">
                Start a Project <i data-lucide="arrow-up-right" class="size-4"></i>
            </a>
        </div>
    </div>
</nav>
