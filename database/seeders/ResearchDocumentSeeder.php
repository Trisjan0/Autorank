<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ResearchDocument;
use Illuminate\Support\Carbon;

class ResearchDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            return;
        }
        foreach ($users as $user) {
            ResearchDocument::create([
                'user_id' => $user->id,
                'title' => 'A Study on the Effects of AI in Education',
                'type' => 'Journal Article',
                'category' => 'Published Paper',
                'link' => 'https://docs.google.com/document/d/example_research1/edit',
                'created_at' => Carbon::now()->subMonths(5),
            ]);

            ResearchDocument::create([
                'user_id' => $user->id,
                'title' => 'Proceedings of the National IT Conference 2024',
                'type' => 'Conference Paper',
                'category' => 'Conference Proceeding',
                'link' => 'https://docs.google.com/document/d/example_research2/edit',
                'created_at' => Carbon::now()->subMonths(2),
            ]);
        }
    }
}
