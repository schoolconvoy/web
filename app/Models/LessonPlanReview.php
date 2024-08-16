<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonPlanReview extends Model
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
