<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\LibrarySubcategory;
use App\Models\User;

class LibrarySubcategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-any LibrarySubcategory');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LibrarySubcategory $librarysubcategory): bool
    {
        return $user->can('view LibrarySubcategory');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create LibrarySubcategory');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LibrarySubcategory $librarysubcategory): bool
    {
        return $user->can('update LibrarySubcategory');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LibrarySubcategory $librarysubcategory): bool
    {
        return $user->can('delete LibrarySubcategory');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LibrarySubcategory $librarysubcategory): bool
    {
        return $user->can('restore LibrarySubcategory');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LibrarySubcategory $librarysubcategory): bool
    {
        return $user->can('force-delete LibrarySubcategory');
    }
}
