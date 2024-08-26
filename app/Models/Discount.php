<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\SessionTermSchoolScope;

#[ScopedBy([SessionTermSchoolScope::class])]
class Discount extends BaseModel
{
    use HasFactory;

    public function fees()
    {
        return $this->belongsToMany(Fee::class, 'discount_fee');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'discount_student', 'discount_id', 'student_id')
                    ->withTimestamps();
    }
}
