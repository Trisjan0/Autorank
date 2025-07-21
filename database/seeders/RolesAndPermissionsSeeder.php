<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar; // Import the registrar

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions to ensure a clean slate
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $allPermissions = [
            'manage users',
            'manage roles',
            'access applications page',
            'view dashboard',
            'view profile',
            'view research documents',
            'view evaluations',
            'view event participations',
        ];

        foreach ($allPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
            $this->command->info("Permission created or found: '{$permissionName}'");
        }

        // --- Roles and Permissions ---
        $rolesWithPermissions = [
            'admin' => [
                'view dashboard',
                'view profile',
                'view research documents',
                'view evaluations',
                'view event participations',
                'access applications page',
                'manage users',
                'manage roles',
            ],
            'user' => [
                'view dashboard',
                'view profile',
            ],
        ];

        foreach ($rolesWithPermissions as $roleName => $permissionsToAssign) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($permissionsToAssign);
            $this->command->info("Role '{$roleName}' created/updated and its permissions synced.");
        }

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::pluck('name')->toArray());
        $this->command->info("Role 'super_admin' created/updated and assigned ALL available permissions.");

        $this->command->info('Roles and permissions seeding completed successfully!');
    }
}
