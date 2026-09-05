@props(['post'])

<article class="journal-card group">
    <a href="{{ route('blog.show', $post->slug) }}" class="journal-card__image" aria-label="Read {{ $post->title }}">
        @if ($post->featured_image)
            @php($cardDimensions = $post->imageDimensions('card'))
            <img
                src="{{ $post->imageUrl('card') }}"
                alt="{{ $post->featured_image_alt }}"
                width="{{ $cardDimensions['width'] }}"
                height="{{ $cardDimensions['height'] }}"
                loading="lazy"
                decoding="async"
            />
        @else
            <span class="journal-card__placeholder" aria-hidden="true">
                <span>V</span><i></i>
            </span>
        @endif
        <span class="journal-card__number">{{ $post->published_at?->format('m.y') }}</span>
    </a>
    <div class="journal-card__body">
        <div class="journal-card__meta">
            <a href="{{ route('blog.category', $post->category->slug) }}">{{ $post->category->name }}</a>
            <span aria-hidden="true">·</span>
            <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->format('M j, Y') }}</time>
        </div>
        <h2><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h2>
        <p>{{ $post->excerpt }}</p>
        <a href="{{ route('blog.show', $post->slug) }}" class="journal-card__link">
            Read the article <span aria-hidden="true">↗</span>
        </a>
    </div>
</article>
