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
        // Custom Seeders
        $this->call([
            RolesAndPermissionsSeeder::class,
            RolesTableSeeder::class,
        ]);

        // comment to unseed? the users
        User::factory(10)->create();
        // If there are no users, the dependent seeders will be skipped
        $this->command->info('Users seeded successfully.');

        // calling the seeders dependent on users
        $this->call([
            EvaluationSeeder::class,
            MaterialSeeder::class,
        ]);
        $this->command->info('Database seeded successfully.');
    }
}
