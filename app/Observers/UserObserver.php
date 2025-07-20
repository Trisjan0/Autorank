<?php

namespace App\Observers;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UserObserver
{
    public function created(User $user): void
    {
        $superAdminEmails = [
            'autorank.team@gmail.com',
        ];

        $adminEmails = [
            '2020103851@pampangastateu.edu.ph',
        ];

        if (in_array($user->email, $superAdminEmails)) {
            $user->assignRole('super_admin');
        } elseif (in_array($user->email, $adminEmails)) {
            $user->assignRole('admin');
        } else {
            $user->assignRole('user');
        }
    }
}
