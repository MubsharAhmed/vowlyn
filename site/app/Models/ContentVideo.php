<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\ContentVideoService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

final class ContentVideo extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'client', 'type', 'note', 'transcript', 'sort_order', 'is_published', 'is_featured'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'is_featured' => 'boolean', 'sort_order' => 'integer', 'duration_seconds' => 'integer'];
    }

    protected static function booted(): void
    {
        self::forceDeleted(function (self $video): void {
            // After commit: a rolled-back database deletion must not destroy the media.
            $video->getConnection()->afterCommit(fn () => app(ContentVideoService::class)->deleteUnused([$video->video_path, $video->poster_path]));
        });
    }

    public function getVideoUrlAttribute(): string
    {
        return $this->mediaUrl($this->video_path);
    }

    public function getPosterUrlAttribute(): string
    {
        return $this->mediaUrl($this->poster_path);
    }

    public function getDurationAttribute(): string
    {
        return sprintf('%02d:%02d', intdiv($this->duration_seconds, 60), $this->duration_seconds % 60);
    }

    private function mediaUrl(string $path): string
    {
        return str_starts_with($path, 'media/content-creation/') ? asset($path) : Storage::disk('public')->url($path);
    }
}
