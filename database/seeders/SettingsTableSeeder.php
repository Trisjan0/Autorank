<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'site_logo'],
            ['value' => 'images/pampanga-state-u-logo-small.png']
        );

        Setting::updateOrCreate(
            ['key' => 'primary_color'],
            ['value' => '#262626']
        );
    }
}
