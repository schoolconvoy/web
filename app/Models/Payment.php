<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\SessionTermSchoolScope;

#[ScopedBy([SessionTermSchoolScope::class])]
class Payment extends BaseModel
{
    use HasFactory;

    public function fees()
    {
        return $this->belongsToMany(Fee::class, 'fee_payments');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'id');
    }
}
