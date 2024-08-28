<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Classes;
use App\Models\User;

class ClassesPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-any Classes');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Classes $classes): bool
    {
        return $user->hasAnyRole([User::$HIGH_PRINCIPAL_ROLE, User::$ELEM_PRINCIPAL_ROLE, User::$ADMIN_ROLE, User::$SUPER_ADMIN_ROLE]) || $user?->teacher_class?->id === $classes->id || $user->can('view Classes', $classes);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create Classes');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Classes $classes): bool
    {
        return $user->can('update Classes');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Classes $classes): bool
    {
        return $user->can('delete Classes');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Classes $classes): bool
    {
        return $user->can('restore Classes');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Classes $classes): bool
    {
        return $user->can('force-delete Classes');
    }
}
