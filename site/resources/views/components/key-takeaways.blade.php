{{-- ============================================================================
    KEY TAKEAWAYS — featured-snippet box (SEO rewrite, 2026)
    Scannable bullet summary that search engines and AI assistants can lift.

    Usage:  <x-key-takeaways :items="['Point one', 'Point two']" />
            <x-key-takeaways :dark="true" :items="[...]" />   (on dark stages)
============================================================================ --}}
@props(['items' => [], 'label' => 'Key takeaways', 'dark' => false])

<aside
    @if (! $attributes->has('data-reveal')) data-reveal @endif
    {{ $attributes->merge([
        'class' => $dark
            ? 'rounded-2xl border border-white/12 bg-white/[0.04] backdrop-blur-xl p-6'
            : 'rounded-2xl bg-lavender-100/60 border border-lavender-300/70 p-6',
    ]) }}>
    <p class="eyebrow mb-3 {{ $dark ? '!text-primary-300' : '' }}">{{ $label }}</p>
    <ul class="space-y-2 text-sm leading-relaxed list-disc pl-5 {{ $dark ? 'text-white/75 marker:text-primary-300' : 'text-on-surface/80 marker:text-primary-600' }}">
        @foreach ($items as $item)
            <li>{{ $item }}</li>
        @endforeach
    </ul>
</aside>
