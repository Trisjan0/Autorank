<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AssignRolesToExistingUsers extends Command
{

    protected $signature = 'roles:assign-existing'; // command: php artisan roles:assign-existing

    protected $description = 'Assigns roles to existing users based on email rules (similar to UserObserver).';

    public function handle(): int
    {
        $this->info('Starting role assignment for existing users...');

        $superAdminEmails = [
            'autorank.team@gmail.com',
        ];

        $adminEmails = [
            '2020103851@pampangatasteu.edu.ph',
        ];

        // Get all users (paginate if too many)
        $users = User::all();

        $this->withProgressBar($users, function ($user) use ($superAdminEmails, $adminEmails) {
            $assignedRole = 'user';

            if (in_array($user->email, $superAdminEmails)) {
                $assignedRole = 'super_admin';
            } elseif (in_array($user->email, $adminEmails)) {
                $assignedRole = 'admin';
            }

            $user->syncRoles($assignedRole);
        });

        $this->newLine();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->info('Role assignment completed for all existing users.');
        $this->info('Spatie permission cache cleared.');

        return Command::SUCCESS;
    }
}
