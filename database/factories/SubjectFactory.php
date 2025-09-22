<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subjects = [
            'Tafsir Al-Quran',
            'Hadist',
            'Fiqh',
            'Tasawuf',
            'Aqidah',
            'Sejarah Islam',
            'Bahasa Arab',
            'Naskah Kuno',
            'Sastra Islam',
            'Filsafat Islam',
            'Pendidikan Islam',
            'Hukum Islam',
            'Ekonomi Islam',
            'Politik Islam',
        ];

        return [
            'name' => fake()->randomElement($subjects),
            'description' => fake()->optional(0.7)->sentence(),
            'type' => fake()->randomElement(['topic', 'genre', 'classification', 'discipline']),
            'metadata' => [
                'level' => fake()->optional(0.4)->randomElement(['basic', 'intermediate', 'advanced']),
                'language' => fake()->optional(0.3)->randomElement(['arabic', 'indonesian', 'malay', 'javanese']),
            ],
        ];
    }
}
