<?php

namespace Database\Seeders;

use App\Models\Instance;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instances = Instance::all();

        if ($instances->isEmpty()) {
            $this->command->warn('Make sure InstanceSeeder has been run first.');

            return;
        }

        // Create items for each instance (some instances may have multiple copies)
        foreach ($instances as $instance) {
            $itemCount = rand(1, 2); // Each instance can have 1-2 items (copies)

            for ($i = 0; $i < $itemCount; $i++) {
                Item::factory()->create([
                    'instance_id' => $instance->id,
                ]);
            }
        }

        // Create some additional items
        Item::factory(15)->create([
            'instance_id' => fn() => $instances->random()->id,
        ]);
    }
}
