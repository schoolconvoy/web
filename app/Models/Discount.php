<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\SessionTermSchoolScope;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy([SessionTermSchoolScope::class])]
class Discount extends BaseModel
{
    use HasFactory, SoftDeletes;

    public function fees()
    {
        return $this->belongsToMany(Fee::class, 'discount_student_fee', 'discount_id', 'fee_id')
                    ->using(DiscountStudentFee::class)
                    ->withPivot('student_id')
                    ->withTimestamps();
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'discount_student_fee', 'discount_id', 'student_id')
                    ->using(DiscountStudentFee::class)
                    ->withPivot('fee_id')
                    ->withTimestamps();
    }
}
