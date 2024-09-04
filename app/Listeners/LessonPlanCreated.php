<?php

namespace App\Listeners;

use App\Events\LessonPlanCreated as LessonPlanCreatedEvent;
use App\Models\Scopes\ClassScope;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\LessonPlanCreated as LessonPlanCreatedNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

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
        $lessonPlan = $event->lessonPlan;

        $roles = [
            User::$ADMIN_ROLE,
            User::$SUPER_ADMIN_ROLE
        ];

        if ($event->lessonPlan->teacher->isHighSchool()) {
            array_push($roles, User::$HIGH_PRINCIPAL_ROLE);
        } else {
            array_push($roles, User::$ELEM_PRINCIPAL_ROLE);
        }

        $admins = User::role($roles)->withoutGlobalScope(ClassScope::class);
        $admins = $admins->get();

        Notification::send($admins, new LessonPlanCreatedNotification($lessonPlan));
    }
}
