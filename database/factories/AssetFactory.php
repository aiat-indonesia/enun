<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = fake()->word() . '.' . fake()->randomElement(['pdf', 'jpg', 'png', 'docx', 'txt']);
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt' => 'text/plain',
        ];
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        return [
            'disk' => 'local',
            'path' => 'assets/' . fake()->year() . '/' . fake()->month() . '/' . $filename,
            'filename' => $filename,
            'mime_type' => $mimeTypes[$extension] ?? 'application/octet-stream',
            'size' => fake()->numberBetween(1024, 50 * 1024 * 1024), // 1KB to 50MB
            'extracted_text' => fake()->optional(0.6)->paragraph(),
            'metadata' => [
                'uploaded_by' => fake()->name(),
                'description' => fake()->optional(0.5)->sentence(),
                'tags' => fake()->optional(0.4)->words(3),
            ],
        ];
    }
}
