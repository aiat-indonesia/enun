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
        // Create admin user (check if exists first)
        $admin = User::firstOrCreate(
            ['email' => 'admin@enun.test'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        // Create test user (check if exists first)
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // Create additional users only if we don't have enough
        $currentUserCount = User::count();
        if ($currentUserCount < 10) {
            User::factory(10 - $currentUserCount)->create();
        }

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
