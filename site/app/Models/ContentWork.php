<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\ContentWorkImageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

final class ContentWork extends Model
{
    use SoftDeletes;

    protected $fillable = ['discipline', 'title', 'client', 'description', 'image_alt', 'link_url', 'sort_order', 'is_published'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'is_published' => 'boolean', 'width' => 'integer', 'height' => 'integer', 'size_bytes' => 'integer'];
    }

    protected static function booted(): void
    {
        self::forceDeleted(function (self $work): void {
            $work->getConnection()->afterCommit(fn () => app(ContentWorkImageService::class)->deleteUnused([$work->image_path, $work->thumbnail_path]));
        });
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->image_path);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->thumbnail_path ?: $this->image_path);
    }

    private function mediaUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return str_starts_with($path, 'media/content-creation/') ? asset($path) : Storage::disk('public')->url($path);
    }
}
