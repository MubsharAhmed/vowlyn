@if ($record = $getRecord())
    <div>
        <p class="mb-2 text-sm font-medium">Current image</p>
        @if ($record->thumbnail_url)
            <img src="{{ $record->thumbnail_url }}" alt="{{ $record->image_alt ?: 'Current creative-work image' }}"
                 style="width:100%;max-width:360px;max-height:280px;object-fit:cover;border-radius:12px" />
        @else
            <p class="text-sm text-gray-500">No image is attached yet. Upload one below to replace the designed project plate.</p>
        @endif
        <p class="mt-2 text-sm">{{ $record->is_published ? 'Published on the content page.' : 'Draft — not listed on the website.' }}</p>
    </div>
@endif
