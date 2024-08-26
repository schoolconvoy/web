<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\SessionTermSchoolScope;

#[ScopedBy([SessionTermSchoolScope::class])]
class LessonPlanTopic extends BaseModel
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
