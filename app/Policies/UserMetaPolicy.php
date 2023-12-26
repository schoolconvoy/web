<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\UserMeta;
use App\Models\User;

class UserMetaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-any UserMeta');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserMeta $usermeta): bool
    {
        return $user->can('view UserMeta');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create UserMeta');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserMeta $usermeta): bool
    {
        return $user->can('update UserMeta');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserMeta $usermeta): bool
    {
        return $user->can('delete UserMeta');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserMeta $usermeta): bool
    {
        return $user->can('restore UserMeta');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserMeta $usermeta): bool
    {
        return $user->can('force-delete UserMeta');
    }
}
