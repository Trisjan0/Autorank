<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Prevents duplicate entries if seeder is run multiple times
        DB::table('positions')->truncate();

        $positions = [
            ['title' => 'Instructor I', 'reqs' => [60, 10, 20, 10]],
            ['title' => 'Instructor II', 'reqs' => [60, 10, 20, 10]],
            ['title' => 'Instructor III', 'reqs' => [60, 10, 20, 10]],
            ['title' => 'Assistant Professor I', 'reqs' => [50, 20, 20, 10]],
            ['title' => 'Assistant Professor II', 'reqs' => [50, 20, 20, 10]],
            ['title' => 'Assistant Professor III', 'reqs' => [50, 20, 20, 10]],
            ['title' => 'Assistant Professor IV', 'reqs' => [50, 20, 20, 10]],
            ['title' => 'Associate Professor I', 'reqs' => [40, 30, 20, 10]],
            ['title' => 'Associate Professor II', 'reqs' => [40, 30, 20, 10]],
            ['title' => 'Associate Professor III', 'reqs' => [40, 30, 20, 10]],
            ['title' => 'Associate Professor IV', 'reqs' => [40, 30, 20, 10]],
            ['title' => 'Associate Professor V', 'reqs' => [40, 30, 20, 10]],
            ['title' => 'Professor I', 'reqs' => [30, 40, 20, 10]],
            ['title' => 'Professor II', 'reqs' => [30, 40, 20, 10]],
            ['title' => 'Professor III', 'reqs' => [30, 40, 20, 10]],
            ['title' => 'Professor IV', 'reqs' => [30, 40, 20, 10]],
            ['title' => 'Professor V', 'reqs' => [30, 40, 20, 10]],
            ['title' => 'Professor VI', 'reqs' => [30, 40, 20, 10]],
            ['title' => 'College / University Professor', 'reqs' => [20, 50, 20, 10]],
        ];

        foreach ($positions as $position) {
            Position::create([
                'title' => $position['title'],
                'requirements' => [
                    'Teaching' => $position['reqs'][0],
                    'Research' => $position['reqs'][1],
                    'Extension Service' => $position['reqs'][2],
                    'Professional Development' => $position['reqs'][3],
                ],
                // By default, no positions are available until toggled by an admin
                'is_available' => false
            ]);
        }
    }
}
