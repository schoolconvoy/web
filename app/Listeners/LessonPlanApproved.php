<?php

namespace App\Listeners;

use App\Events\LessonPlanApproved as LessonPlanApprovedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\LessonPlanApprovedNotification;
use Illuminate\Support\Facades\Log;

class LessonPlanApproved
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
    public function handle(LessonPlanApprovedEvent $event): void
    {
        $teacher = $event->lessonPlan->teacher;

        $teacher->notify(new LessonPlanApprovedNotification($event));
    }
}
