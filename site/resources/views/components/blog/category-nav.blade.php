@props(['categories', 'active' => null])

<nav class="journal-categories" aria-label="Blog categories">
    <a href="{{ route('blog.index') }}" @class(['is-active' => $active === null])>All notes</a>
    @foreach ($categories as $category)
        <a href="{{ route('blog.category', $category->slug) }}" @class(['is-active' => $active === $category->slug])>
            {{ $category->name }}
            <sup>{{ str_pad((string) $category->published_posts_count, 2, '0', STR_PAD_LEFT) }}</sup>
        </a>
    @endforeach
</nav>
