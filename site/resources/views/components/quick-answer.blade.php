{{-- ============================================================================
    QUICK ANSWER — "In short" GEO block (SEO rewrite, 2026)
    A single, plainly-worded paragraph an AI engine can lift verbatim when
    answering "what is Vowlyn?" style queries. Light-card styling per
    DESIGN.md (lavender surface, 16px radius, indigo-tinted shadow).

    Usage:  <x-quick-answer>Your one-paragraph answer…</x-quick-answer>
            <x-quick-answer :dark="true">…</x-quick-answer>   (on dark stages)
============================================================================ --}}
@props(['label' => 'In short', 'dark' => false])

<aside
    @if (! $attributes->has('data-reveal')) data-reveal @endif
    {{ $attributes->merge([
        'class' => $dark
            ? 'rounded-2xl border border-white/12 bg-white/[0.04] backdrop-blur-xl p-5 sm:p-6'
            : 'rounded-2xl border border-primary-200/80 bg-white/70 backdrop-blur-sm p-5 sm:p-6 shadow-[0_18px_40px_-24px_rgba(75,0,130,0.28)]',
    ]) }}>
    <p class="eyebrow mb-2 {{ $dark ? '!text-primary-300' : '' }}">{{ $label }}</p>
    <p class="text-[15px] sm:text-base leading-relaxed {{ $dark ? 'text-white/80' : 'text-on-surface/80' }}">{{ $slot }}</p>
</aside>
