<?php

namespace App\Listeners;

use App\Events\StudentPromoted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\PromotionNotification;

class SendPromotionNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StudentPromoted $event): void
    {
        $event->user->notify(new PromotionNotification($event->oldClass, $event->newClass));
    }
}
