<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Instance>
 */
class InstanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'work_id' => \App\Models\Work::factory(),
            'label' => fake()->words(2, true).' Edition',
            'publication_year' => fake()->optional(0.8)->year(),
            'format' => fake()->randomElement(['print', 'digital', 'manuscript', 'ebook', 'pdf']),
            'identifiers' => [
                'isbn' => fake()->optional(0.6)->isbn13(),
                'issn' => fake()->optional(0.3)->regexify('[0-9]{4}-[0-9]{4}'),
                'doi' => fake()->optional(0.2)->regexify('[0-9]{4}\.[0-9]{4}/[a-z0-9]+'),
            ],
            'metadata' => [
                'pages' => fake()->optional(0.7)->numberBetween(50, 800),
                'edition' => fake()->optional(0.5)->randomElement(['1st', '2nd', '3rd', 'revised']),
                'notes' => fake()->optional(0.3)->sentence(),
            ],
        ];
    }
}
