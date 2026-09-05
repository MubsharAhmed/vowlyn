{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>Vowlyn Journal</title>
        <link>{{ route('blog.index') }}</link>
        <description>Practical notes on building software, AI products, cloud systems, and digital experiences.</description>
        <language>en</language>
        <atom:link href="{{ route('blog.feed') }}" rel="self" type="application/rss+xml" />
        @foreach ($posts as $post)
            <item>
                <title>{{ $post->title }}</title>
                <link>{{ route('blog.show', $post->slug) }}</link>
                <guid isPermaLink="true">{{ route('blog.show', $post->slug) }}</guid>
                <pubDate>{{ $post->published_at->toRssString() }}</pubDate>
                <author>{{ $post->author->name }}</author>
                <category>{{ $post->category->name }}</category>
                <description>{{ $post->excerpt }}</description>
            </item>
        @endforeach
    </channel>
</rss>
