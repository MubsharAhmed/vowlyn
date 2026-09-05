@if ($record = $getRecord())
    <div>
        <p class="text-sm font-medium mb-2">Current video · {{ $record->duration }}</p>
        <video controls playsinline preload="none" poster="{{ $record->poster_url }}"
               aria-label="Preview {{ $record->title }}" style="width:100%;max-width:320px;max-height:420px;background:#0d0420;border-radius:12px">
            <source src="{{ $record->video_url }}" type="video/mp4">
        </video>
        <p class="text-sm mt-2">{{ $record->is_published ? 'Published on the content page.' : 'Draft — not listed on the website.' }}</p>
    </div>
@endif
