<?php

namespace App\Policies;

use App\Models\Library;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LibraryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Library $library): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole([User::$SUPER_ADMIN_ROLE, User::$ADMIN_ROLE, User::$TEACHER_ROLE]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Library $library): bool
    {
        return $user->hasAnyRole([User::$SUPER_ADMIN_ROLE, User::$ADMIN_ROLE, User::$TEACHER_ROLE]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Library $library): bool
    {
        return $user->hasAnyRole([User::$SUPER_ADMIN_ROLE, User::$ADMIN_ROLE, User::$TEACHER_ROLE]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Library $library): bool
    {
        return $user->hasAnyRole([User::$SUPER_ADMIN_ROLE, User::$ADMIN_ROLE, User::$TEACHER_ROLE]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Library $library): bool
    {
        return $user->hasAnyRole([User::$SUPER_ADMIN_ROLE, User::$ADMIN_ROLE, User::$TEACHER_ROLE]);
    }
}
