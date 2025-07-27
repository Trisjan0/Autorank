<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleRanks = [
            'super_admin' => 1,
            'admin'       => 2,
            'user'        => 3,
        ];

        foreach ($roleRanks as $roleName => $rank) {
            $role = Role::where('name', $roleName)->first();

            if ($role) {
                $role->update(['rank' => $rank]);
                $this->command->info("Updated role '{$roleName}' with rank {$rank}.");
            } else {
                $this->command->warn("Role '{$roleName}' not found. Please ensure it exists before seeding ranks.");
            }
        }

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command->info('Role ranks seeded successfully!');
    }
}
