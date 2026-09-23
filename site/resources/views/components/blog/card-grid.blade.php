@props(['posts', 'featured' => null, 'paginator' => null])

@php
    /* The archive pages pass a paginator as $posts, the related-posts strip does
       not — so the pager is detected rather than demanded. */
    $pager = $paginator ?? ($posts instanceof \Illuminate\Contracts\Pagination\Paginator ? $posts : null);

    /* A featured note is pinned inside the first page only, and the index query
       keeps it out of the paginator itself, so it is added back to the total.
       Load-more appends real cards, which is what keeps the count honest. */
    $pinnedPost = ($featured && (! $pager || $pager->onFirstPage())) ? $featured : null;
    $cardCount = ($pinnedPost ? 1 : 0) + $posts->count();
    $shownNotes = $pager ? $cardCount + ($pager->currentPage() - 1) * $pager->perPage() : $cardCount;
    $totalNotes = $pager ? $pager->total() + ($featured ? 1 : 0) : $cardCount;
@endphp

@if ($cardCount > 0)
    <div class="journal-card-grid" data-journal-cards>
        @if ($pinnedPost)
            <x-blog.post-card :post="$pinnedPost" :featured="true" :priority="true" />
        @endif
        @foreach ($posts as $post)
            <x-blog.post-card :post="$post" />
        @endforeach
    </div>

    @if ($pager?->hasPages())
        <div class="journal-more" data-journal-more>
            @if ($pager->hasMorePages())
                <a class="journal-more__button" href="{{ $pager->nextPageUrl() }}" rel="next" data-journal-more-button>
                    Load more notes
                    <i data-lucide="arrow-down" class="size-4" aria-hidden="true"></i>
                </a>
            @endif
            <p class="journal-more__status" role="status" aria-live="polite" data-journal-more-status>
                Showing <span data-journal-more-shown>{{ $shownNotes }}</span> of {{ $totalNotes }} {{ \Illuminate\Support\Str::plural('note', $totalNotes) }}
            </p>
        </div>

        {{-- Without JavaScript the numbered links stay in charge, and they keep
             every deeper page reachable by crawlers. --}}
        <nav class="journal-pagination" aria-label="Pagination" data-journal-pagination>
            {{ $pager->onEachSide(1)->links() }}
        </nav>
    @endif
@else
    {{ $empty ?? '' }}
@endif
