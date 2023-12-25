<?php

namespace App\Models;

use App\Events\StudentIsAbsent;
use App\Events\StudentIsLate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Attendance extends Model
{
    use HasFactory;

    const PRESENT = 1;
    const ABSENT = 2;
    const LATE = 3;

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'id');
    }

    public function subject()
    {
        // Implement Subject - Attendance relationship.
    }

    public function students()
    {
        return $this->belongsTo(User::class, 'student_id', 'id');
    }

    /**
     * Get attendance record for today
     */
    public static function exists($studentId)
    {
        $date = date('Y-m-d');
        $record = self::where('student_id', $studentId)
                        ->whereRaw('date(created_at) = ?', [$date])
                        ->exists();

        return $record;
    }

    public static function initiate($class)
    {
        $class = Classes::find($class);

        if (!$class)
        {
            Log::debug('Invalid class id supplied for attendance ' . $class);
            return;
        }

        $students = $class->users;

        foreach($students as $student)
        {
            if (self::exists($student->id))
            {
                continue;
            }

            self::create([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'status' => false
            ]);
        }

        return self::where('class_id', $class->id)
                ->whereRaw('date(created_at) = ?', [date('Y-m-d')]);
    }

    /**
     * Dispatch events when attendance is updated
     */
    // protected static function booted(): void
    // {
    //     static::updated(function (Attendance $attendance) {
    //         if ($attendance->status === self::LATE)
    //         {
    //             Log::debug('student is late ' . print_r($attendance, true));
    //             StudentIsLate::dispatch($attendance);
    //         }
    //         else if ($attendance->status === self::ABSENT)
    //         {
    //             StudentIsAbsent::dispatch($attendance);
    //         }
    //     });
    // }
}
