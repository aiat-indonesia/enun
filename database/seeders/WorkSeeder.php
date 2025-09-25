<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Place;
use App\Models\Work;
use Illuminate\Database\Seeder;

class WorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some agents and places for relationships
        $agents = Agent::all();
        $places = Place::all();

        if ($agents->isEmpty() || $places->isEmpty()) {
            $this->command->warn('Make sure AgentSeeder and PlaceSeeder have been run first.');

            return;
        }

        // Create some famous Nusantara Islamic works
        $works = [
            [
                'type' => 'tafsir',
                'title' => 'Tarjuman al-Mustafid',
                'slug' => 'tarjuman-al-mustafid',
                'summary' => ['Tafsir Al-Quran dalam bahasa Melayu karya Abdurrauf as-Singkili'],
                'author_id' => $agents->where('name', 'Abdurrauf as-Singkili')->first()->id ?? $agents->random()->id,
                'place_id' => $places->where('name', 'Aceh')->first()->id ?? $places->random()->id,
                'contributors' => [
                    ['name' => 'Unknown Scribe', 'role' => 'translator', 'notes' => 'Manuscript copyist'],
                ],
                'creation_year' => [
                    'from' => 1675,
                    'to' => 1690,
                    'circa' => false,
                ],
                'metadata' => [
                    'keywords' => ['tafsir', 'quran', 'melayu', 'aceh'],
                    'notes' => 'One of the earliest Malay Quranic commentaries',
                    'language' => 'Malay (Jawi)',
                ],
                'status' => 'published',
                'visibility' => 'public',
                'published_at' => now()->subYears(10),
            ],
            [
                'type' => 'manuscript',
                'title' => 'Syair Burung Pingai',
                'slug' => 'syair-burung-pingai',
                'summary' => ['Syair sufi karya Hamzah Fansuri tentang perjalanan spiritual'],
                'author_id' => $agents->where('name', 'Hamzah Fansuri')->first()->id ?? $agents->random()->id,
                'place_id' => $places->where('name', 'Aceh')->first()->id ?? $places->random()->id,
                'contributors' => [],
                'creation_year' => [
                    'from' => 1580,
                    'to' => 1590,
                    'circa' => true,
                ],
                'metadata' => [
                    'keywords' => ['sufism', 'poetry', 'mysticism', 'malay'],
                    'notes' => 'Famous Sufi poem by Hamzah Fansuri',
                    'language' => 'Malay',
                ],
                'status' => 'published',
                'visibility' => 'public',
                'published_at' => now()->subYears(5),
            ],
            [
                'type' => 'book',
                'title' => 'Sabil al-Muhtadin',
                'slug' => 'sabil-al-muhtadin',
                'summary' => ['Kitab fiqh dalam bahasa Banjar oleh Muhammad Arsyad al-Banjari'],
                'author_id' => $agents->where('name', 'Muhammad Arsyad al-Banjari')->first()->id ?? $agents->random()->id,
                'place_id' => $places->where('name', 'Palembang')->first()->id ?? $places->random()->id,
                'contributors' => [
                    ['name' => 'Local Publisher', 'role' => 'editor', 'notes' => 'Modern edition'],
                ],
                'creation_year' => [
                    'from' => 1780,
                    'to' => 1790,
                    'circa' => false,
                ],
                'metadata' => [
                    'keywords' => ['fiqh', 'islamic law', 'banjar', 'indonesia'],
                    'notes' => 'Important fiqh work in Banjarese language',
                    'language' => 'Banjar/Malay',
                ],
                'status' => 'published',
                'visibility' => 'public',
                'published_at' => now()->subYears(15),
            ],
            [
                'type' => 'tafsir',
                'title' => 'Marah Labid',
                'slug' => 'marah-labid',
                'summary' => ['Tafsir Al-Quran karya Muhammad Nawawi al-Bantani'],
                'author_id' => $agents->where('name', 'Muhammad Nawawi al-Bantani')->first()->id ?? $agents->random()->id,
                'place_id' => $places->where('name', 'Bandung')->first()->id ?? $places->random()->id,
                'contributors' => [],
                'creation_year' => [
                    'from' => 1880,
                    'to' => 1890,
                    'circa' => false,
                ],
                'metadata' => [
                    'keywords' => ['tafsir', 'quran', 'arabic', 'banten'],
                    'notes' => 'Comprehensive Quranic commentary',
                    'language' => 'Arabic',
                ],
                'status' => 'published',
                'visibility' => 'public',
                'published_at' => now()->subYears(20),
            ],
            [
                'type' => 'manuscript',
                'title' => 'Hikayat Raja-raja Pasai',
                'slug' => 'hikayat-raja-raja-pasai',
                'summary' => ['Hikayat tentang sejarah Kesultanan Pasai'],
                'author_id' => $agents->random()->id,
                'place_id' => $places->where('name', 'Aceh')->first()->id ?? $places->random()->id,
                'contributors' => [
                    ['name' => 'Unknown Chronicler', 'role' => 'compiler', 'notes' => 'Original compiler unknown'],
                ],
                'creation_year' => [
                    'from' => 1350,
                    'to' => 1400,
                    'circa' => true,
                ],
                'metadata' => [
                    'keywords' => ['history', 'pasai', 'sultanate', 'chronicle'],
                    'notes' => 'Historical chronicle of Pasai Sultanate',
                    'language' => 'Old Malay',
                ],
                'status' => 'published',
                'visibility' => 'public',
                'published_at' => now()->subYears(25),
            ],
        ];

        foreach ($works as $workData) {
            $work = Work::create($workData);

            // Attach some random agent relationships
            if (rand(1, 3) == 1) { // 33% chance
                $randomAgents = $agents->random(rand(1, 2));
                foreach ($randomAgents as $agent) {
                    $work->agents()->attach($agent->id, [
                        'role' => fake()->randomElement(['editor', 'translator', 'commentator', 'illustrator']),
                    ]);
                }
            }

            // Add some tags
            $work->attachTags([
                fake()->randomElement(['Classical', 'Medieval', 'Modern']),
                fake()->randomElement(['Aceh', 'Java', 'Sumatra', 'Sulawesi']),
                fake()->randomElement(['Arabic', 'Malay', 'Javanese']),
            ]);
        }

        // Create additional works using factory
        $additionalWorks = Work::factory(30)->create([
            'author_id' => fn() => $agents->random()->id,
            'place_id' => fn() => $places->random()->id,
        ]);

        // Attach random agent relationships to factory-created works
        foreach ($additionalWorks as $work) {
            if (rand(1, 4) == 1) { // 25% chance
                $randomAgents = $agents->random(rand(1, 3));
                foreach ($randomAgents as $agent) {
                    $work->agents()->attach($agent->id, [
                        'role' => fake()->randomElement(['editor', 'translator', 'commentator', 'compiler', 'illustrator']),
                    ]);
                }
            }

            // Add tags to factory works
            $work->attachTags([
                fake()->randomElement(['Classical', 'Medieval', 'Modern', 'Contemporary']),
                fake()->randomElement(['Religious', 'Historical', 'Literary', 'Legal']),
            ]);
        }
    }
}
