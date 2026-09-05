<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BlogAuthor;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<BlogPost> */
final class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(7);

        return [
            'blog_author_id' => BlogAuthor::factory(),
            'blog_category_id' => BlogCategory::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->paragraph(3),
            'content' => [
                'type' => 'doc',
                'content' => [
                    ['type' => 'heading', 'attrs' => ['level' => 2], 'content' => [['type' => 'text', 'text' => 'What you need to know']]],
                    ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => fake()->paragraphs(3, true)]]],
                ],
            ],
            'tags' => ['engineering', 'strategy'],
            'status' => BlogPost::STATUS_DRAFT,
            'published_at' => null,
            'is_featured' => false,
            'is_indexable' => true,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => BlogPost::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (): array => [
            'status' => BlogPost::STATUS_SCHEDULED,
            'published_at' => now()->addDay(),
        ]);
    }
}
