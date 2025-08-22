<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Evaluation;
use Illuminate\Support\Carbon;

class EvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users to associate evaluations with
        $users = User::all();

        // If there are no users, the seeder will be skipped
        if ($users->isEmpty()) {
            $this->command->info('No users found, skipping EvaluationSeeder.');
            return;
        }

        foreach ($users as $user) {
            Evaluation::create([
                'user_id' => $user->id,
                'title' => 'Student Evaluation of Teaching',
                'category' => 'Instruction',
                'score' => 92.50,
                'link' => 'https://docs.google.com/document/d/randomBS1/edit',
                'created_at' => Carbon::now()->subMonths(2),
                'updated_at' => Carbon::now()->subMonths(2),
            ]);

            Evaluation::create([
                'user_id' => $user->id,
                'title' => 'Peer Observation Report',
                'category' => 'Instruction',
                'score' => 88.00,
                'link' => 'https://docs.google.com/document/d/randomBS2/edit',
                'created_at' => Carbon::now()->subMonths(1),
                'updated_at' => Carbon::now()->subMonths(1),
            ]);

            Evaluation::create([
                'user_id' => $user->id,
                'title' => 'Annual Performance Review',
                'category' => 'Overall Performance',
                'score' => 95.75,
                'link' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
