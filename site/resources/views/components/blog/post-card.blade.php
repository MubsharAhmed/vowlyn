@props(['post', 'featured' => false, 'priority' => false])

@php($postUrl = route('blog.show', $post->slug))

<article @class(['journal-card', 'journal-card--featured' => $featured])>
    <div class="journal-card__image">
        @if ($post->featured_image)
            @php($cardDimensions = $post->imageDimensions('card'))
            <img
                src="{{ $post->imageUrl('card') }}"
                alt="{{ $post->featured_image_alt }}"
                width="{{ $cardDimensions['width'] }}"
                height="{{ $cardDimensions['height'] }}"
                @if ($priority) fetchpriority="high" @else loading="lazy" @endif
                decoding="async"
            />
        @else
            <span class="journal-card__placeholder" aria-hidden="true">
                <span>V</span><i></i>
            </span>
        @endif
        @if ($featured)
            <span class="journal-card__flag">Editors’ pick</span>
        @endif
    </div>
    <div class="journal-card__body">
        <div class="journal-card__meta">
            <a href="{{ route('blog.category', $post->category->slug) }}">{{ $post->category->name }}</a>
            <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->format('M j, Y') }}</time>
        </div>
        <h2><a href="{{ $postUrl }}">{{ $post->title }}</a></h2>
        <p class="journal-card__excerpt">{{ $post->excerpt }}</p>
        <p class="journal-card__byline">
            By <a href="{{ route('blog.author', $post->author->slug) }}">{{ $post->author->name }}</a>
        </p>
    </div>
</article>
