{{-- ============================================================================
   NAVIGATION — Dark glass pill nav (logo wordmark is white → needs dark bg)
   Floating, centered, transforms on scroll. Mobile = full-screen overlay.
   ============================================================================ --}}
<nav
    data-nav
    class="fixed top-4 inset-x-0 z-40 px-4 transition-all duration-500 [&.is-scrolled>div]:bg-surface-ink/85 [&.is-scrolled>div]:backdrop-blur-xl"
>
    <div class="mx-auto max-w-7xl pill-dark rounded-full px-4 sm:px-6 py-3 flex items-center justify-between shadow-xl transition-all duration-500">

        {{-- Logo --}}
        <a href="{{ url('/#home') }}" class="flex items-center gap-2.5 shrink-0 group" aria-label="Vowlyn home">
            <img src="{{ asset('brand/vowlyn-logo.png') }}" alt="Vowlyn" class="h-9 w-auto sm:h-10 select-none" draggable="false" />
        </a>

        {{-- Desktop links --}}
        <ul class="hidden lg:flex items-center gap-1 text-sm font-medium">
            @foreach ([
                ['Services', route('services')],
                ['Portfolio', route('portfolio')],
                ['Content', route('content-creation')],
                ['Journal', route('blog.index')],
                ['Marketing', route('performance-marketing')],
                ['About', route('about')],
                ['Why Us', route('why-us')],
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

            {{-- Native mobile disclosure: interactive before the JS bundle boots. --}}
            <details data-mobile-menu class="group lg:hidden">
                <summary
                    aria-label="Toggle navigation"
                    class="grid place-items-center size-10 rounded-full bg-white/8 text-white hover:bg-white/15 transition cursor-pointer list-none [&::-webkit-details-marker]:hidden"
                >
                    <i data-lucide="menu" class="size-5 group-open:hidden"></i>
                    <i data-lucide="x" class="hidden size-5 group-open:block"></i>
                </summary>

            </details>
        </div>
    </div>

    {{-- Keep the fixed panel outside the blurred pill. A transformed/filtered ancestor
         becomes the containing block for fixed children in mobile Safari. --}}
    <div data-mobile-panel class="hidden fixed inset-x-0 top-[5.25rem] bottom-0 bg-surface-ink/98 backdrop-blur-2xl overflow-y-auto overscroll-contain">
        <ul class="flex flex-col p-6 gap-1 text-on-inverse font-display text-2xl sm:text-3xl font-medium">
            @foreach ([
                ['Services', route('services')],
                ['Portfolio', route('portfolio')],
                ['Content', route('content-creation')],
                ['Journal', route('blog.index')],
                ['Marketing', route('performance-marketing')],
                ['About', route('about')],
                ['Why Us', route('why-us')],
                ['Contact', url('/#contact')],
            ] as [$label, $href])
                <li>
                    <a href="{{ $href }}" class="block py-3 border-b border-white/8 hover:text-primary-300 transition">
                        {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="p-6 pt-2">
            <a href="{{ url('/#contact') }}" class="btn btn-primary w-full">
                Start a Project <i data-lucide="arrow-up-right" class="size-4"></i>
            </a>
        </div>
    </div>
</nav>
