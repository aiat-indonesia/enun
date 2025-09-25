<?php

namespace Database\Seeders;

use App\Models\Agent;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some notable Islamic scholars and authors from Nusantara
        $agents = [
            [
                'name' => 'Hamzah Fansuri',
                'type' => 'person',
                'metadata' => [
                    'birth_year' => 1550,
                    'death_year' => 1600,
                    'origin' => 'Aceh',
                    'specialty' => 'Sufism, Poetry',
                    'notes' => 'Famous Sufi poet and scholar from Aceh',
                ],
            ],
            [
                'name' => 'Syamsuddin as-Samatrani',
                'type' => 'person',
                'metadata' => [
                    'birth_year' => 1550,
                    'death_year' => 1630,
                    'origin' => 'Aceh',
                    'specialty' => 'Sufism, Theology',
                    'notes' => 'Prominent Sufi scholar and writer',
                ],
            ],
            [
                'name' => 'Abdurrauf as-Singkili',
                'type' => 'person',
                'metadata' => [
                    'birth_year' => 1615,
                    'death_year' => 1693,
                    'origin' => 'Aceh',
                    'specialty' => 'Tafsir, Hadith, Fiqh',
                    'notes' => 'Author of Tarjuman al-Mustafid tafsir',
                ],
            ],
            [
                'name' => 'Muhammad Arsyad al-Banjari',
                'type' => 'person',
                'metadata' => [
                    'birth_year' => 1710,
                    'death_year' => 1812,
                    'origin' => 'Banjarmasin',
                    'specialty' => 'Fiqh, Theology',
                    'notes' => 'Famous Banjarese Islamic scholar',
                ],
            ],
            [
                'name' => 'Muhammad Nawawi al-Bantani',
                'type' => 'person',
                'metadata' => [
                    'birth_year' => 1813,
                    'death_year' => 1897,
                    'origin' => 'Banten',
                    'specialty' => 'Tafsir, Hadith, Fiqh',
                    'notes' => 'Prolific writer and scholar from Banten',
                ],
            ],
            [
                'name' => 'Ahmad Khatib al-Minangkabawi',
                'type' => 'person',
                'metadata' => [
                    'birth_year' => 1860,
                    'death_year' => 1916,
                    'origin' => 'Minangkabau',
                    'specialty' => 'Fiqh, Islamic Law',
                    'notes' => 'Influential Minangkabau scholar',
                ],
            ],
            [
                'name' => 'KH. Hasyim Asyari',
                'type' => 'person',
                'metadata' => [
                    'birth_year' => 1871,
                    'death_year' => 1947,
                    'origin' => 'Jombang, East Java',
                    'specialty' => 'Islamic Education, Fiqh',
                    'notes' => 'Founder of Nahdlatul Ulama',
                ],
            ],
            [
                'name' => 'Ahmad Dahlan',
                'type' => 'person',
                'metadata' => [
                    'birth_year' => 1868,
                    'death_year' => 1923,
                    'origin' => 'Yogyakarta',
                    'specialty' => 'Islamic Reform, Education',
                    'notes' => 'Founder of Muhammadiyah',
                ],
            ],
            [
                'name' => 'Balai Pustaka',
                'type' => 'organization',
                'metadata' => [
                    'established' => 1917,
                    'location' => 'Jakarta',
                    'type' => 'Publisher',
                    'notes' => 'Indonesian government publisher',
                ],
            ],
            [
                'name' => 'Pustaka Progressif',
                'type' => 'organization',
                'metadata' => [
                    'established' => 1950,
                    'location' => 'Surabaya',
                    'type' => 'Publisher',
                    'notes' => 'Islamic book publisher',
                ],
            ],
        ];

        foreach ($agents as $agent) {
            Agent::create($agent);
        }

        // Create additional agents using factory
        Agent::factory(20)->create();
    }
}
