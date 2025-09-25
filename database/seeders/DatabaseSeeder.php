<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@enun.test',
        ]);

        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create additional users
        User::factory(8)->create();

        $this->call([
            ShieldSeeder::class,
            PlaceSeeder::class,
            AgentSeeder::class,
            WorkSeeder::class,
            InstanceSeeder::class,
            ItemSeeder::class,
        ]);

        $this->command->info('Database seeding completed successfully!');
        $this->command->info('Admin user: admin@enun.test');
        $this->command->info('Test user: test@example.com');
    }
}
