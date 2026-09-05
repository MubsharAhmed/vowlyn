@php($tone = ($config['tone'] ?? 'primary') === 'dark' ? 'dark' : 'primary')
<aside class="blog-inline-cta {{ $tone === 'dark' ? 'blog-inline-cta--dark' : '' }}" aria-label="Next step">
    <div>
        <p class="blog-inline-cta__eyebrow">Build with Vowlyn</p>
        <h2>{{ $config['heading'] ?? 'Turn the idea into a product.' }}</h2>
        <p>{{ $config['description'] ?? 'Tell us what you are planning and we will map the clearest path forward.' }}</p>
    </div>
    <a href="{{ $config['button_url'] ?? url('/#contact') }}" class="blog-inline-cta__button">
        {{ $config['button_label'] ?? 'Start a project' }}
        <span aria-hidden="true">↗</span>
    </a>
</aside>
