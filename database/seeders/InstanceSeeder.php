<?php

namespace Database\Seeders;

use App\Models\Instance;
use App\Models\Work;
use Illuminate\Database\Seeder;

class InstanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $works = Work::all();

        if ($works->isEmpty()) {
            $this->command->warn('Make sure WorkSeeder has been run first.');

            return;
        }

        // Create instances for each work (some works may have multiple instances)
        foreach ($works as $work) {
            $instanceCount = rand(1, 3); // Each work can have 1-3 instances

            for ($i = 0; $i < $instanceCount; $i++) {
                Instance::factory()->create([
                    'work_id' => $work->id,
                ]);
            }
        }

        // Create some additional instances
        Instance::factory(20)->create([
            'work_id' => fn() => $works->random()->id,
        ]);
    }
}
