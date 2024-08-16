<?php

namespace App\Listeners;

use App\Events\LessonPlanReviewUpdated as LessonPlanReviewUpdatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class LessonPlanReviewUpdated
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
    public function handle(LessonPlanReviewUpdatedEvent $event): void
    {
    }
}
