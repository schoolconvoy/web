<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonPlanTopic extends Model
{
    use HasFactory;

    public function lessonPlan()
    {
        return $this->belongsTo(LessonPlan::class);
    }

    public function subject()
    {
        return $this->hasMany(Subject::class);
    }

    public function teacher()
    {
        return $this->hasMany(User::class);
    }
}
