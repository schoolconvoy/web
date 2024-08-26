<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\SessionTermSchoolScope;

#[ScopedBy([SessionTermSchoolScope::class])]
class LessonPlanReview extends BaseModel
{
    use HasFactory;

    protected $casts = [
        'status' => 'boolean',
    ];

    public function lessonPlan()
    {
        return $this->belongsTo(LessonPlan::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by', 'id');
    }
}
