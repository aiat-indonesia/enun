<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_identifier' => fake()->optional(0.8)->regexify('[A-Z]{2}[0-9]{6}'),
            'location' => fake()->optional(0.9)->randomElement([
                'Main Library - Floor 2',
                'Digital Collection',
                'Rare Books Section',
                'Archive Storage',
                'Reading Room',
            ]),
            'call_number' => fake()->optional(0.7)->regexify('[0-9]{3}\.[0-9]{2} [A-Z]{3}'),
            'availability' => fake()->randomElement(['available', 'checked_out', 'reserved', 'damaged', 'missing']),
            'metadata' => [
                'condition' => fake()->randomElement(['excellent', 'good', 'fair', 'poor']),
                'acquisition_date' => fake()->optional(0.5)->date(),
                'notes' => fake()->optional(0.3)->sentence(),
            ],
        ];
    }
}
