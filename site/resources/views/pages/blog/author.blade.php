@extends('layouts.app')

@section('title', $author->name.' — Author at Vowlyn Journal')
@section('description', $author->bio ?: 'Read articles and field notes from '.$author->name.' at Vowlyn.')
@section('canonical', $posts->currentPage() > 1 ? route('blog.author', $author->slug).'?page='.$posts->currentPage() : route('blog.author', $author->slug))
@section('robots', $posts->total() < 2 ? 'noindex,follow,max-image-preview:large' : 'index,follow,max-image-preview:large')

@push('head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'ProfilePage',
            'mainEntity' => [
                '@type' => 'Person',
                'name' => $author->name,
                'jobTitle' => $author->job_title,
                'description' => $author->bio,
                'url' => route('blog.author', $author->slug),
                'image' => $author->avatar_url,
                'sameAs' => array_values($author->same_as ?? []),
                'worksFor' => ['@type' => 'Organization', 'name' => 'Vowlyn', 'url' => route('home')],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}
    </script>
@endpush

@section('content')
    <div class="journal-shell">
        <header class="journal-author-hero">
            <div class="journal-grid" aria-hidden="true"></div>
            <div class="container-vw relative">
                <nav aria-label="Breadcrumb" class="journal-breadcrumb">
                    <a href="{{ route('home') }}">Home</a><span>/</span>
                    <a href="{{ route('blog.index') }}">Journal</a><span>/</span>
                    <span>{{ $author->name }}</span>
                </nav>
                <div class="journal-author-hero__layout">
                    <div class="journal-author-hero__portrait">
                        @if ($author->avatar_url)
                            <img src="{{ $author->avatar_url }}" alt="Portrait of {{ $author->name }}" width="480" height="480" />
                        @else
                            <span aria-hidden="true">{{ collect(explode(' ', $author->name))->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('') }}</span>
                        @endif
                    </div>
                    <div>
                        <p>Author / {{ $author->job_title ?: 'Vowlyn studio' }}</p>
                        <h1>{{ $author->name }}</h1>
                        @if ($author->bio)<div class="journal-author-hero__bio">{{ $author->bio }}</div>@endif
                    </div>
                </div>
            </div>
        </header>
        <section class="journal-index">
            <div class="container-vw">
                <div class="journal-section-heading"><span>Articles by {{ $author->name }}</span><span>{{ $posts->total() }} published</span></div>
                <div class="journal-card-grid">
                    @foreach ($posts as $post)<x-blog.post-card :post="$post" />@endforeach
                </div>
                <div class="journal-pagination">{{ $posts->onEachSide(1)->links() }}</div>
            </div>
        </section>
    </div>
@endsection
