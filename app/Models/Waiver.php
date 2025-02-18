<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\SessionTermSchoolScope;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy([SessionTermSchoolScope::class])]
class Waiver extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'end_date' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function fees()
    {
        return $this->belongsToMany(Fee::class, 'waiver_fees')
                    ->withTimestamps();
    }

    public function isActive()
    {
        return $this->end_date === null || $this->end_date->isFuture();
    }
}
