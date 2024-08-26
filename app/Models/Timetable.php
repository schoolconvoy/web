<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\SessionTermSchoolScope;

#[ScopedBy([SessionTermSchoolScope::class])]
class Timetable extends BaseModel
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
