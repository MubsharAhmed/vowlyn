<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BlogAuthor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<BlogAuthor> */
final class BlogAuthorFactory extends Factory
{
    protected $model = BlogAuthor::class;

    public function definition(): array
    {
        $name = fake()->name();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 9999),
            'job_title' => fake()->randomElement(['Product Strategist', 'Software Engineer', 'Creative Director']),
            'bio' => fake()->paragraph(),
            'same_as' => [],
            'is_active' => true,
        ];
    }
}
