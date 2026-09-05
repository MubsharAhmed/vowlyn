@php($imagePath = $config['path'] ?? null)
@if ($imagePath)
    <figure class="blog-editorial-image">
        <img
            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($imagePath) }}"
            alt="{{ $config['alt'] ?? '' }}"
            loading="lazy"
            decoding="async"
        />
        @if (filled($config['caption'] ?? null) || filled($config['credit'] ?? null))
            <figcaption>
                <span>{{ $config['caption'] ?? '' }}</span>
                @if (filled($config['credit'] ?? null))
                    <cite>{{ $config['credit'] }}</cite>
                @endif
            </figcaption>
        @endif
    </figure>
@endif
