<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Attendance;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CalendarWidget extends FullCalendarWidget
{
    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views on the calendar.
    */
    public function fetchEvents(array $fetchInfo): array
    {
        // You can use $fetchInfo to filter events by date.
        // This method should return an array of event-like objects. See: https://github.com/saade/filament-fullcalendar/blob/3.x/#returning-events
        // You can also return an array of EventData objects. See: https://github.com/saade/filament-fullcalendar/blob/3.x/#the-eventdata-class

        $ward_id = Cache::get('ward');

        return Attendance::query()
        ->where('created_at', '>=', $fetchInfo['start'])
        ->where('status', '!=', 0)
        ->where('student_id', $ward_id)
        ->get()
        ->map(
            fn (Attendance $attendance) => [
                'title' => Attendance::status($attendance->status),
                'start' => $attendance->created_at,
                'textColor' => 'white', // default is 'black
                'description' => 'Lecture',
                'allDay' => true,
                'backgroundColor' => Attendance::statusColor($attendance->status),
                'display' => 'background'
            ]
        )
        ->all();
    }


}
