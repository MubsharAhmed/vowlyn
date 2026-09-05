@extends('layouts.app')

@section('title', 'Follow the Vowlyn Journal')
@section('description', 'Add the Vowlyn Journal to your preferred RSS reader and receive new field notes without an algorithm or inbox clutter.')
@section('canonical', route('blog.subscribe'))
@section('robots', 'noindex,follow')

@section('content')
    <div class="journal-shell">
        <header class="journal-utility-header">
            <div class="container-vw">
                <nav class="journal-breadcrumb" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}">Home</a><span>/</span>
                    <a href="{{ route('blog.index') }}">Journal</a><span>/</span>
                    <span>Follow</span>
                </nav>
                <span class="journal-utility-header__label">Follow the Journal</span>
                <h1>Read new articles in an app you control.</h1>
                <p>RSS delivers new Journal posts without a newsletter signup, algorithm, or inbox clutter.</p>
            </div>
        </header>

        <section class="journal-follow-guide">
            <div class="container-vw">
                <div class="journal-follow__layout">
                    <div class="journal-follow__aside">
                        <h2>What is RSS?</h2>
                        <p>RSS is a simple, private way to follow websites. Your reader checks for new articles and keeps them together in one place.</p>
                        <a href="{{ route('blog.index') }}">Back to all articles</a>
                    </div>
                    <div class="journal-follow__card" aria-labelledby="rss-guide-title">
                        <div class="journal-follow__card-top"><span>RSS quick start</span><span>01—03</span></div>
                        <h2 id="rss-guide-title">Add the Journal to your reader</h2>
                        <ol>
                            <li><span>01</span><p>Copy the Journal feed address below.</p></li>
                            <li><span>02</span><p>Open Feedly, Inoreader, NetNewsWire, Reeder, or another RSS app.</p></li>
                            <li><span>03</span><p>Choose “add feed” and paste the address.</p></li>
                        </ol>
                        <code>{{ route('blog.feed') }}</code>
                        <div class="journal-follow__actions">
                            <button type="button" data-copy-url="{{ route('blog.feed') }}" data-copy-success="Feed address copied" aria-label="Copy Journal RSS feed address">Copy feed address <span aria-hidden="true">↗</span></button>
                            <a href="https://feedly.com/i/subscription/feed/{{ urlencode(route('blog.feed')) }}" target="_blank" rel="noopener noreferrer">Add to Feedly <span aria-hidden="true">↗</span></a>
                        </div>
                        <a class="journal-follow__raw" href="{{ route('blog.feed') }}" target="_blank">Technical: open the raw XML feed</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
