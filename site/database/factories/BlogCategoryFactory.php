<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<BlogCategory> */
final class BlogCategoryFactory extends Factory
{
    protected $model = BlogCategory::class;

    public function definition(): array
    {
        $name = fake()->randomElement([
            'AI Engineering', 'Web Applications', 'SaaS Strategy', 'Cloud & DevOps',
            'Mobile Products', 'Enterprise Security', 'Content Strategy',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 99999),
            'description' => fake()->sentence(16),
            'is_active' => true,
        ];
    }
}
