<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the admin can manage (view/update) the given user.
     */
    public function manage(Admin $admin, User $user): bool
    {
        return $admin->isSuperAdmin() || $user->assigned_admin_id === $admin->id;
    }
}
