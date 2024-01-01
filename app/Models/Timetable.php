<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    public function subjects()
    {
        return $this->belongsTo(Subject::class);
    }
}


// $timetable->subjects
// $timetable->day_of_week
