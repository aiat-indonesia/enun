<?php

namespace Database\Seeders;

use App\Models\Place;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample places in Indonesia (Nusantara region)
        $places = [
            [
                'name' => 'Jakarta',
                'slug' => 'jakarta',
                'type' => 'city',
                'parent_id' => null,
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'metadata' => [
                    'region' => 'DKI Jakarta',
                    'country' => 'Indonesia',
                ],
            ],
            [
                'name' => 'Yogyakarta',
                'slug' => 'yogyakarta',
                'type' => 'city',
                'parent_id' => null,
                'latitude' => -7.7956,
                'longitude' => 110.3695,
                'metadata' => [
                    'region' => 'DIY Yogyakarta',
                    'country' => 'Indonesia',
                ],
            ],
            [
                'name' => 'Surakarta',
                'slug' => 'surakarta',
                'type' => 'city',
                'parent_id' => null,
                'latitude' => -7.5755,
                'longitude' => 110.8243,
                'metadata' => [
                    'region' => 'Jawa Tengah',
                    'country' => 'Indonesia',
                ],
            ],
            [
                'name' => 'Surabaya',
                'slug' => 'surabaya',
                'type' => 'city',
                'parent_id' => null,
                'latitude' => -7.2575,
                'longitude' => 112.7521,
                'metadata' => [
                    'region' => 'Jawa Timur',
                    'country' => 'Indonesia',
                ],
            ],
            [
                'name' => 'Bandung',
                'slug' => 'bandung',
                'type' => 'city',
                'parent_id' => null,
                'latitude' => -6.9175,
                'longitude' => 107.6191,
                'metadata' => [
                    'region' => 'Jawa Barat',
                    'country' => 'Indonesia',
                ],
            ],
            [
                'name' => 'Medan',
                'slug' => 'medan',
                'type' => 'city',
                'parent_id' => null,
                'latitude' => 3.5952,
                'longitude' => 98.6722,
                'metadata' => [
                    'region' => 'Sumatera Utara',
                    'country' => 'Indonesia',
                ],
            ],
            [
                'name' => 'Makassar',
                'slug' => 'makassar',
                'type' => 'city',
                'parent_id' => null,
                'latitude' => -5.1477,
                'longitude' => 119.4327,
                'metadata' => [
                    'region' => 'Sulawesi Selatan',
                    'country' => 'Indonesia',
                ],
            ],
            [
                'name' => 'Palembang',
                'slug' => 'palembang',
                'type' => 'city',
                'parent_id' => null,
                'latitude' => -2.9761,
                'longitude' => 104.7754,
                'metadata' => [
                    'region' => 'Sumatera Selatan',
                    'country' => 'Indonesia',
                ],
            ],
            [
                'name' => 'Aceh',
                'slug' => 'aceh',
                'type' => 'province',
                'parent_id' => null,
                'latitude' => 4.6951,
                'longitude' => 96.7494,
                'metadata' => [
                    'region' => 'Sumatera',
                    'country' => 'Indonesia',
                ],
            ],
            [
                'name' => 'Malacca',
                'slug' => 'malacca',
                'type' => 'city',
                'parent_id' => null,
                'latitude' => 2.1896,
                'longitude' => 102.2501,
                'metadata' => [
                    'region' => 'Malacca',
                    'country' => 'Malaysia',
                ],
            ],
        ];

        foreach ($places as $place) {
            Place::create($place);
        }
    }
}
