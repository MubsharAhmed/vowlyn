<?php

declare(strict_types=1);

namespace App\Models;

use App\Filament\RichContent\BlogCallToActionBlock;
use App\Filament\RichContent\BlogImageBlock;
use App\Services\BlogImageService;
use Database\Factories\BlogPostFactory;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'blog_author_id', 'blog_category_id', 'title', 'slug', 'excerpt', 'content', 'tags',
    'status', 'published_at', 'is_featured', 'featured_image', 'featured_image_alt',
    'featured_image_caption', 'featured_image_credit', 'seo_title', 'seo_description',
    'canonical_url', 'is_indexable', 'social_title', 'social_description', 'social_image',
])]
final class BlogPost extends Model implements HasRichContent
{
    /** @use HasFactory<BlogPostFactory> */
    use HasFactory;

    use InteractsWithRichContent;
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_ARCHIVED = 'archived';

    /** @var array<int, string> */
    private array $replacedMedia = [];

    /** @var array<int, string> */
    private array $removedContentMedia = [];

    protected static function booted(): void
    {
        self::creating(function (self $post): void {
            $post->slug = self::uniqueSlug($post->slug ?: $post->title);
        });

        self::updating(function (self $post): void {
            if ($post->isDirty('slug')) {
                $post->slug = self::uniqueSlug($post->slug ?: $post->title, $post->getKey());
            }

            foreach (['featured_image', 'social_image'] as $attribute) {
                if ($post->isDirty($attribute) && filled($post->getOriginal($attribute))) {
                    $post->replacedMedia[] = (string) $post->getOriginal($attribute);
                }
            }

            if ($post->isDirty('content')) {
                $oldContent = json_decode((string) $post->getRawOriginal('content'), true);
                $service = app(BlogImageService::class);
                $post->removedContentMedia = array_values(array_diff(
                    $service->contentImagePaths(is_array($oldContent) ? $oldContent : []),
                    $service->contentImagePaths($post->content),
                ));
            }
        });

        self::created(fn (self $post) => app(BlogImageService::class)->generateFor($post));

        self::updated(function (self $post): void {
            if ($post->wasChanged('slug')) {
                $oldSlug = (string) $post->getOriginal('slug');

                if (filled($oldSlug) && $oldSlug !== $post->slug) {
                    BlogPostSlugRedirect::query()->updateOrCreate(
                        ['slug' => $oldSlug],
                        ['blog_post_id' => $post->getKey()],
                    );
                }

                BlogPostSlugRedirect::query()->where('slug', $post->slug)->delete();
            }

            foreach ($post->replacedMedia as $path) {
                app(BlogImageService::class)->deleteSourceAndConversions($path);
            }

            app(BlogImageService::class)->deleteContentImages($post->removedContentMedia);

            if ($post->wasChanged(['featured_image', 'social_image'])) {
                app(BlogImageService::class)->generateFor($post);
            }
        });

        self::forceDeleted(function (self $post): void {
            foreach (array_filter([$post->featured_image, $post->social_image]) as $path) {
                app(BlogImageService::class)->deleteSourceAndConversions((string) $path);
            }
            app(BlogImageService::class)->deleteContentImages(
                app(BlogImageService::class)->contentImagePaths($post->content),
            );
        });
    }

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'tags' => 'array',
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'is_indexable' => 'boolean',
        ];
    }

    protected function setUpRichContent(): void
    {
        $this->registerRichContent('content')
            ->json()
            ->customBlocks([
                'Marketing' => [BlogCallToActionBlock::class],
                'Media' => [BlogImageBlock::class],
            ]);
    }

    /** @return BelongsTo<BlogAuthor, $this> */
    public function author(): BelongsTo
    {
        return $this->belongsTo(BlogAuthor::class, 'blog_author_id');
    }

    /** @return BelongsTo<BlogCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    /** @return HasMany<BlogPostSlugRedirect, $this> */
    public function slugRedirects(): HasMany
    {
        return $this->hasMany(BlogPostSlugRedirect::class);
    }

    /** @param Builder<BlogPost> $query */
    public function scopePublished(Builder $query): void
    {
        $query
            ->whereIn('status', [self::STATUS_PUBLISHED, self::STATUS_SCHEDULED])
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isPubliclyVisible(): bool
    {
        return in_array($this->status, [self::STATUS_PUBLISHED, self::STATUS_SCHEDULED], true)
            && $this->published_at?->isPast();
    }

    public function resolvedSeoTitle(): string
    {
        return filled($this->seo_title) ? $this->seo_title : "{$this->title} | Vowlyn";
    }

    public function resolvedSeoDescription(): string
    {
        return filled($this->seo_description) ? $this->seo_description : Str::limit($this->excerpt, 160, '');
    }

    public function resolvedSocialTitle(): string
    {
        return filled($this->social_title) ? $this->social_title : $this->resolvedSeoTitle();
    }

    public function resolvedSocialDescription(): string
    {
        return filled($this->social_description) ? $this->social_description : $this->resolvedSeoDescription();
    }

    public function canonicalUrl(): string
    {
        return filled($this->canonical_url) ? $this->canonical_url : route('blog.show', $this->slug);
    }

    public function imageUrl(string $preset = 'hero'): string
    {
        return app(BlogImageService::class)->urlFor($this, $preset);
    }

    /** @return array{width:int,height:int} */
    public function imageDimensions(string $preset = 'hero'): array
    {
        return app(BlogImageService::class)->dimensions($preset);
    }

    /** @return array<string, string> */
    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    private static function uniqueSlug(string $value, int|string|null $ignoreId = null): string
    {
        $base = Str::slug($value) ?: Str::lower(Str::random(8));
        $slug = $base;
        $suffix = 2;

        while (self::withTrashed()
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
