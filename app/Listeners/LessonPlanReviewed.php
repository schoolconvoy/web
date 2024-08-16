<?php

namespace App\Listeners;

use App\Events\LessonPlanReviewed as LessonPlanReviewedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\LessonPlanReview as LessonPlanReviewNotification;
use Illuminate\Support\Facades\Log;

class LessonPlanReviewed
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
    public function handle(LessonPlanReviewedEvent $event): void
    {
        $teacher = $event->review->lessonPlan->teacher;

        $teacher->notify(new LessonPlanReviewNotification($event));
    }
}
