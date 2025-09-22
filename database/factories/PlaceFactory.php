<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Place>
 */
class PlaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['province', 'regency', 'city', 'village']);

        // Indonesia bounds approximately
        $lat = fake()->latitude(-11, 6);
        $lng = fake()->longitude(95, 141);

        return [
            'name' => fake()->city(),
            'type' => $type,
            'lat' => $lat,
            'lng' => $lng,
            'geojson_polygon' => fake()->optional(0.3)->randomElements([
                'type' => 'Polygon',
                'coordinates' => [[[
                    [$lng - 0.1, $lat - 0.1],
                    [$lng + 0.1, $lat - 0.1],
                    [$lng + 0.1, $lat + 0.1],
                    [$lng - 0.1, $lat + 0.1],
                    [$lng - 0.1, $lat - 0.1],
                ]]],
            ]),
            'metadata' => [
                'population' => fake()->optional(0.5)->numberBetween(1000, 1000000),
                'established' => fake()->optional(0.4)->year(),
            ],
        ];
    }
}
