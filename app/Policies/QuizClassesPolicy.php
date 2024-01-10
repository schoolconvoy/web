<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\QuizClasses;
use App\Models\User;

class QuizClassesPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-any QuizClasses');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, QuizClasses $quizclasses): bool
    {
        return $user->can('view QuizClasses');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create QuizClasses');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, QuizClasses $quizclasses): bool
    {
        return $user->can('update QuizClasses');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, QuizClasses $quizclasses): bool
    {
        return $user->can('delete QuizClasses');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, QuizClasses $quizclasses): bool
    {
        return $user->can('restore QuizClasses');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, QuizClasses $quizclasses): bool
    {
        return $user->can('force-delete QuizClasses');
    }
}
