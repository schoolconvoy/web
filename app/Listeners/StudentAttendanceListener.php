<?php

namespace App\Listeners;

use App\Events\StudentAttendance;
use App\Notifications\AttendanceAlert;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class StudentAttendanceListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        Log::debug('Dispatched student is absent event');
    }

    /**
     * Handle the event.
     */
    public function handle(StudentAttendance $event): void
    {
        $parent = $event->attendance->students->parent()->first();

        if ($parent)
        {
            $notified = $parent->notify(new AttendanceAlert($event->attendance));
        }
        else
        {
            Log::debug('Student ' . $event->attendance->students->firstname . ' has no parent. Parent array: ' . print_r($parent, true));
        }
    }
}
