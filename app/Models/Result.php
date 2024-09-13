<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\SessionTermSchoolScope;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy([SessionTermSchoolScope::class])]
class Result extends BaseModel
{
    use HasFactory, SoftDeletes;

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }
}
