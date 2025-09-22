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
            'slug' => str($title)->slug(),
            'title' => $title,
            'subtitle' => fake()->optional(0.3)->sentence(),
            'languages' => [fake()->randomElement(['id', 'ar', 'ms', 'jv'])],
            'summary' => fake()->optional(0.8)->paragraph(),
            'type' => fake()->randomElement(['manuscript', 'tafsir', 'book', 'journal', 'article']),
            'status' => fake()->randomElement(['draft', 'review', 'published']),
            'metadata' => [
                'keywords' => fake()->words(3),
                'notes' => fake()->optional(0.4)->sentence(),
            ],
            'alternative_titles' => fake()->optional(0.3)->words(3),
            'external_identifiers' => [
                'doi' => fake()->optional(0.2)->regexify('[0-9]{4}\.[0-9]{4}/[a-z0-9]+'),
                'isbn' => fake()->optional(0.3)->isbn13(),
            ],
            'seller_links' => fake()->optional(0.4)->randomElements([
                ['name' => 'Gramedia', 'url' => 'https://gramedia.com/example'],
                ['name' => 'Tokopedia', 'url' => 'https://tokopedia.com/example'],
            ], mt_rand(0, 2)),
        ];
    }
}
