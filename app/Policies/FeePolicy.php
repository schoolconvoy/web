<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Fee;
use App\Models\User;

class FeePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(User::$PARENT_ROLE);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Fee $fee): bool
    {
        return $user->can('view Fee');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create Fee');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Fee $fee): bool
    {
        return $user->can('update Fee');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Fee $fee): bool
    {
        return $user->can('delete Fee');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Fee $fee): bool
    {
        return $user->can('restore Fee');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Fee $fee): bool
    {
        return $user->can('force-delete Fee');
    }
}
