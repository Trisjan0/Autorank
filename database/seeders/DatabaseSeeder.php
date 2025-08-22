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
            EvaluationSeeder::class,
            MaterialSeeder::class,
        ]);

        // comment to unseed? the users
        User::factory(10)->create();
    }
}
