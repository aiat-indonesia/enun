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
            'instance_id' => \App\Models\Instance::factory(),
            'identifier' => fake()->optional(0.8)->regexify('[A-Z]{2}[0-9]{6}'),
            'condition' => fake()->optional(0.9)->randomElement(['excellent', 'good', 'fair', 'poor']),
            'metadata' => [
                'acquisition_date' => fake()->optional(0.5)->date(),
                'notes' => fake()->optional(0.3)->sentence(),
                'shelf_location' => fake()->optional(0.7)->regexify('[A-Z]{1}[0-9]{2}-[0-9]{3}'),
            ],
        ];
    }
}
