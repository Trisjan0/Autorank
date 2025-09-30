<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FacultyRank;
use App\Models\FacultyRankKraWeight;

class FacultyRankSeeder extends Seeder
{
    public function run(): void
    {
        $ranks = [
            'Instructor I' => [60, 10, 20, 10],
            'Instructor II' => [60, 10, 20, 10],
            'Instructor III' => [60, 10, 20, 10],
            'Assistant Professor I' => [50, 20, 20, 10],
            'Assistant Professor II' => [50, 20, 20, 10],
            'Assistant Professor III' => [50, 20, 20, 10],
            'Assistant Professor IV' => [50, 20, 20, 10],
            'Associate Professor I' => [40, 30, 20, 10],
            'Associate Professor II' => [40, 30, 20, 10],
            'Associate Professor III' => [40, 30, 20, 10],
            'Associate Professor IV' => [40, 30, 20, 10],
            'Associate Professor V' => [40, 30, 20, 10],
            'Professor I' => [30, 40, 20, 10],
            'Professor II' => [30, 40, 20, 10],
            'Professor III' => [30, 40, 20, 10],
            'Professor IV' => [30, 40, 20, 10],
            'Professor V' => [30, 40, 20, 10],
            'Professor VI' => [30, 40, 20, 10],
            'College / University Professor' => [20, 50, 20, 10],
        ];

        $kraNames = [
            'KRA 1 Instruction',
            'KRA 2 Research',
            'KRA 3 Extension',
            'KRA 4 Professional Development',
        ];

        foreach ($ranks as $rankName => $weights) {
            $rank = FacultyRank::create(['rank_name' => $rankName]);

            foreach ($weights as $index => $weight) {
                FacultyRankKraWeight::create([
                    'faculty_rank_id' => $rank->id,
                    'kra_name' => $kraNames[$index],
                    'weight' => $weight,
                ]);
            }
        }
    }
}
