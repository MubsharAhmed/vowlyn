@php($imagePath = $config['path'] ?? null)
<div style="padding: .75rem; border-radius: .75rem; background: #f8fafc; border: 1px solid #e2e8f0;">
    @if ($imagePath)
        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($imagePath) }}" alt="{{ $config['alt'] ?? '' }}" style="width: 100%; max-height: 18rem; object-fit: contain; border-radius: .5rem;" />
    @endif
    <p style="margin-top: .5rem; color: #475569;">Alt: {{ $config['alt'] ?? 'Missing' }}</p>
</div>
