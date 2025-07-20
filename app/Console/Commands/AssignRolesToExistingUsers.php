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

        $users = User::all(); // gets all users, paginate when too many

        $this->withProgressBar($users, function ($user) {
            // Call the centralized method on each user
            $user->assignDefaultRoleByEmail();
        });

        $this->newLine();

        // Clear Spatie's permission cache after assigning roles
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->info('Role assignment completed for all existing users.');
        $this->info('Spatie permission cache cleared.');

        return Command::SUCCESS;
    }
}
