{{-- ============================================================================
    FAQ ACCORDION — People-Also-Ask block (SEO rewrite, 2026)
    Alpine-powered single-open accordion. Pair each visible Q&A with FAQPage
    JSON-LD in the page's <head> (via @push('head')).

    Usage:  <x-faq-accordion :items="[['q' => '…', 'a' => '…'], …]" />
            <x-faq-accordion :dark="true" :items="…" />   (on dark stages)
============================================================================ --}}
@props(['items' => [], 'dark' => false])

<div
    {{ $attributes->merge([
        'class' => 'divide-y ' . ($dark ? 'divide-white/10 border-y border-white/10' : 'divide-on-surface/10 border-y border-on-surface/10'),
    ]) }}
    x-data="{ open: 0 }">
    @foreach ($items as $i => $f)
        <div data-reveal data-reveal-delay="{{ $i * 0.05 }}">
            <button
                type="button"
                @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                :aria-expanded="open === {{ $i }}"
                class="w-full flex items-center justify-between gap-4 py-5 lg:py-6 text-left group">
                <span class="font-display text-lg lg:text-xl transition-colors {{ $dark ? 'text-white group-hover:text-primary-300' : 'text-on-surface group-hover:text-primary-700' }}">{{ $f['q'] }}</span>
                <span
                    class="size-8 grid place-items-center rounded-full border shrink-0 transition-all duration-300 {{ $dark ? 'border-white/20 text-white' : 'border-on-surface/15 text-on-surface' }}"
                    :class="open === {{ $i }} && 'bg-primary-700 !text-white !border-transparent rotate-180'">
                    <i data-lucide="chevron-down" class="size-4"></i>
                </span>
            </button>
            <div x-show="open === {{ $i }}" x-collapse x-cloak>
                <p class="pb-6 pr-12 leading-relaxed {{ $dark ? 'text-white/65' : 'text-on-surface-muted' }}">{{ $f['a'] }}</p>
            </div>
        </div>
    @endforeach
</div>
