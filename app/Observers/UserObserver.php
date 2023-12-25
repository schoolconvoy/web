<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Events\CreatedUser;

class UserObserver
{
    /**
     * Handle the User "creating" event
     * @param User $user
     * @return void
     */
    public function creating(User $user): void
    {
        // This implies that the "User" is added to the school of the
        // user creating their account
        if (auth()->check()) {
            $user->school()->associate(auth()->user()->school);
        }
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Dispatch event
        CreatedUser::dispatch($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
