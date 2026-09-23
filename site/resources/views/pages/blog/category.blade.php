@extends('layouts.app')

@section('title', $category->seo_title ?: $category->name.' Articles | Vowlyn Journal')
@section('description', $category->seo_description ?: $category->description)
@section('canonical', $posts->currentPage() > 1 ? route('blog.category', $category->slug).'?page='.$posts->currentPage() : route('blog.category', $category->slug))
@section('robots', $posts->total() < 2 ? 'noindex,follow,max-image-preview:large' : 'index,follow,max-image-preview:large')

@section('content')
    <div class="journal-shell">
        <header class="journal-archive-hero">
            <div class="journal-grid" aria-hidden="true"></div>
            <div class="container-vw relative">
                <nav aria-label="Breadcrumb" class="journal-breadcrumb">
                    <a href="{{ route('home') }}">Home</a><span>/</span>
                    <a href="{{ route('blog.index') }}">Journal</a><span>/</span>
                    <span>{{ $category->name }}</span>
                </nav>
                <div class="journal-archive-hero__layout">
                    <div>
                        <p>Topic collection / {{ str_pad((string) $posts->total(), 2, '0', STR_PAD_LEFT) }}</p>
                        <h1>{{ $category->name }}</h1>
                    </div>
                    <p>{{ $category->description }}</p>
                </div>
            </div>
        </header>
        <section class="journal-index">
            <div class="container-vw">
                <x-blog.category-nav :categories="$categories" :active="$category->slug" />

                <x-blog.card-grid :posts="$posts">
                    <x-slot:empty>
                        <div class="journal-empty"><span>Empty collection</span><h2>No published notes here yet.</h2></div>
                    </x-slot:empty>
                </x-blog.card-grid>
            </div>
        </section>
    </div>
@endsection
