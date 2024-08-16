<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonPlan extends Model
{
    use HasFactory;

    const STATUS_LABELS = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'in_review' => 'In Review',
    ];

    const STATUS_COLORS = [
        'pending' => 'yellow',
        'approved' => 'green',
        'rejected' => 'red',
        'in_review' => 'blue',
    ];

    const AWAITING_REVIEW = 'pending';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';
    const IN_REVIEW = 'in_review';

    public function week()
    {
        return $this->belongsTo(Week::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function lessonPlanReviews()
    {
        return $this->hasMany(LessonPlanReview::class);
    }

    public function topic()
    {
        return $this->hasOne(LessonPlanTopic::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    public function reviews()
    {
        return $this->hasMany(LessonPlanReview::class);
    }
}
