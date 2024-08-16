<?php

namespace App\Listeners;

use App\Events\LessonPlanCreated as LessonPlanCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\LessonPlanCreated as LessonPlanCreatedNotification;

class LessonPlanCreated
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
    public function handle(LessonPlanCreatedEvent $event): void
    {
        $teacher = $event->lessonPlan->teacher;

        $teacher->notify(new LessonPlanCreatedNotification($event));
    }
}
