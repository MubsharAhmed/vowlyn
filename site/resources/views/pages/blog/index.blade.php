@extends('layouts.app')

@section('title', 'Vowlyn Journal — Software, AI & Product Insights')
@section('description', 'Practical, experience-led notes from Vowlyn on software engineering, AI products, SaaS, mobile apps, cloud systems, security, and digital growth.')
@section('canonical', $posts->currentPage() > 1 ? route('blog.index').'?page='.$posts->currentPage() : route('blog.index'))
@section('og_title', 'Vowlyn Journal — Ideas Built in the Real World')
@section('og_description', 'Useful field notes on shipping digital products that perform, scale, and earn attention.')

@push('head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Blog',
            'name' => 'Vowlyn Journal',
            'description' => 'Practical notes on software, AI products, SaaS, mobile, cloud, and digital growth.',
            'url' => route('blog.index'),
            'publisher' => ['@type' => 'Organization', 'name' => 'Vowlyn', 'url' => route('home')],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}
    </script>
@endpush

@section('content')
    <div class="journal-shell">
        <header class="journal-home-header">
            <div class="container-vw">
                <div class="journal-home-header__layout">
                    <div>
                        <span class="journal-home-header__label">Vowlyn Journal</span>
                        <h1>Practical notes for teams building digital products.</h1>
                    </div>
                    <div class="journal-home-header__intro">
                        <p>Experience-led writing about software, AI, product strategy, and growth. Clear lessons from real delivery work.</p>
                        <div class="journal-home-header__actions">
                            <a href="#journal-latest">Browse articles</a>
                            <a href="{{ route('blog.subscribe') }}">Follow via RSS</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <section class="journal-index" id="journal-latest">
            <div class="container-vw">
                <x-blog.category-nav :categories="$categories" />

                @if ($featured)
                    <div class="journal-section-heading"><span>Featured article</span><span>Editor’s pick</span></div>
                    <article class="journal-feature">
                        <a href="{{ route('blog.show', $featured->slug) }}" class="journal-feature__media">
                            @if ($featured->featured_image)
                                @php($heroDimensions = $featured->imageDimensions('hero'))
                                <img src="{{ $featured->imageUrl('hero') }}" alt="{{ $featured->featured_image_alt }}" width="{{ $heroDimensions['width'] }}" height="{{ $heroDimensions['height'] }}" fetchpriority="high" decoding="async" />
                            @else
                                <span class="journal-feature__placeholder" aria-hidden="true">V</span>
                            @endif
                        </a>
                        <div class="journal-feature__copy">
                            <div class="journal-feature__label"><span>Featured dispatch</span><span>01</span></div>
                            <a href="{{ route('blog.category', $featured->category->slug) }}" class="journal-feature__category">{{ $featured->category->name }}</a>
                            <h2><a href="{{ route('blog.show', $featured->slug) }}">{{ $featured->title }}</a></h2>
                            <p>{{ $featured->excerpt }}</p>
                            <div class="journal-feature__byline">
                                <span>By <a href="{{ route('blog.author', $featured->author->slug) }}">{{ $featured->author->name }}</a></span>
                                <time datetime="{{ $featured->published_at->toDateString() }}">{{ $featured->published_at->format('M j, Y') }}</time>
                            </div>
                        </div>
                    </article>
                @endif

                <div class="journal-section-heading">
                    <span>Latest thinking</span>
                    <span>{{ str_pad((string) $posts->total(), 2, '0', STR_PAD_LEFT) }} articles</span>
                </div>

                @if ($posts->isNotEmpty())
                    <div class="journal-card-grid">
                        @foreach ($posts as $post)
                            <x-blog.post-card :post="$post" />
                        @endforeach
                    </div>
                    <div class="journal-pagination">{{ $posts->onEachSide(1)->links() }}</div>
                @else
                    <div class="journal-empty">
                        <span>Issue 00</span>
                        <h2>The first field note is being written.</h2>
                        <p>Original ideas take longer than recycled ones. Check back soon.</p>
                    </div>
                @endif
            </div>
        </section>

        <section class="journal-subscribe" aria-labelledby="journal-subscribe-title">
            <div class="container-vw">
                <div>
                    <span>Follow the Journal</span>
                    <h2 id="journal-subscribe-title">New articles, without inbox noise.</h2>
                </div>
                <div>
                    <p>Add the Journal to your preferred RSS reader, or talk to the studio when an idea is ready to become a real product.</p>
                    <div class="journal-subscribe__actions">
                        <a href="{{ route('blog.subscribe') }}">Follow the Journal <span aria-hidden="true">↗</span></a>
                        <a href="{{ url('/#contact') }}">Start a project <span aria-hidden="true">↗</span></a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
