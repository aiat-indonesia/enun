<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Work>
 */
class WorkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(mt_rand(2, 5), true);

        return [
            'type' => fake()->randomElement(['manuscript', 'tafsir', 'book', 'journal', 'article']),
            'title' => $title,
            'slug' => str($title)->slug(),
            'summary' => fake()->optional(0.8)->paragraphs(2, true),
            'contributors' => fake()->optional(0.3)->randomElements([
                ['name' => fake()->name(), 'role' => 'editor'],
                ['name' => fake()->name(), 'role' => 'translator'],
            ], mt_rand(0, 2)),
            'creation_year' => [
                'from' => fake()->optional(0.7)->numberBetween(1400, 1900),
                'to' => fake()->optional(0.3)->numberBetween(1400, 1900),
                'circa' => fake()->optional(0.2)->boolean(),
            ],
            'metadata' => [
                'keywords' => fake()->words(3),
                'notes' => fake()->optional(0.4)->sentence(),
            ],
            'status' => fake()->randomElement(['draft', 'in_review', 'published', 'archived']),
            'visibility' => fake()->randomElement(['private', 'public', 'restricted']),
            'published_at' => fake()->optional(0.3)->dateTimeThisYear(),
        ];
    }
}
