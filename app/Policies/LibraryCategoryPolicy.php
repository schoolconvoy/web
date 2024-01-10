<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\LibraryCategory;
use App\Models\User;

class LibraryCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-any LibraryCategory');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LibraryCategory $librarycategory): bool
    {
        return $user->can('view LibraryCategory');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create LibraryCategory');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LibraryCategory $librarycategory): bool
    {
        return $user->can('update LibraryCategory');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LibraryCategory $librarycategory): bool
    {
        return $user->can('delete LibraryCategory');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LibraryCategory $librarycategory): bool
    {
        return $user->can('restore LibraryCategory');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LibraryCategory $librarycategory): bool
    {
        return $user->can('force-delete LibraryCategory');
    }
}
