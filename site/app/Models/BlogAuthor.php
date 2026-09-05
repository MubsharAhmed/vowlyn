<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\BlogAuthorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'slug', 'job_title', 'bio', 'avatar', 'same_as', 'is_active'])]
final class BlogAuthor extends Model
{
    /** @use HasFactory<BlogAuthorFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        self::updated(function (self $author): void {
            if ($author->wasChanged('avatar') && filled($author->getOriginal('avatar'))) {
                Storage::disk('public')->delete((string) $author->getOriginal('avatar'));
            }
        });

        self::deleted(function (self $author): void {
            if ($author->avatar) {
                Storage::disk('public')->delete((string) $author->avatar);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'same_as' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /** @return HasMany<BlogPost, $this> */
    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->avatar
            ? Storage::disk('public')->url($this->avatar)
            : null);
    }
}
