<?php

namespace App\Listeners;

use App\Events\CreatedUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\UserRegistered;
use Illuminate\Support\Facades\Log;

class SendWelcomeNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        Log::debug('Dispatched user welcome notification');
    }

    /**
     * Handle the event.
     */
    public function handle(CreatedUser $event): void
    {
        Log::debug('About to send user a welcome notification ' . print_r($event->user, true));

        try
        {
            $event->user->sendWelcomeNotification($event->user->email);
        }
        catch(\Exception $e)
        {
            Log::debug('Error sending welcome notification ' . $e->getMessage());
        }
    }
}
