<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agent>
 */
class AgentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['person', 'organization', 'publisher']);
        $name = $type === 'person' ? fake()->name() : fake()->company();

        return [
            'name' => $name,
            'type' => $type,
            'birth_date' => $type === 'person' ? fake()->optional(0.6)->date('Y-m-d', '-20 years') : null,
            'death_date' => $type === 'person' ? fake()->optional(0.2)->date('Y-m-d', 'now') : null,
            'biography' => fake()->optional(0.7)->paragraph(),
            'metadata' => [
                'aliases' => fake()->optional(0.3)->words(2),
                'nationality' => fake()->optional(0.5)->country(),
                'specialization' => fake()->optional(0.4)->words(2, true),
            ],
        ];
    }
}
