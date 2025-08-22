<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Material;
use Illuminate\Support\Carbon;


class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users to associate materials with
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('No users found, skipping MaterialSeeder.');
            return;
        }

        foreach ($users as $user) {
            Material::create([
                'user_id' => $user->id,
                'title' => 'Syllabus for IT413',
                'category' => 'Instructional Material',
                'link' => 'https://docs.google.com/document/d/random_syllabus/edit',
                'created_at' => Carbon::now()->subWeeks(3),
                'updated_at' => Carbon::now()->subWeeks(3),
            ]);

            Material::create([
                'user_id' => $user->id,
                'title' => 'Lecture Slides',
                'category' => 'Instructional Material',
                'link' => 'https://docs.google.com/presentation/d/random_slides/edit',
                'created_at' => Carbon::now()->subWeeks(2),
                'updated_at' => Carbon::now()->subWeeks(2),
            ]);
        }
    }
}
