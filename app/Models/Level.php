<?php

namespace App\Models;

use App\Models\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\SessionTermSchoolScope;

#[ScopedBy([SchoolScope::class])]
class Level extends BaseModel
{
    use HasFactory;

    public function class()
    {
        $this->hasOne(Classes::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class)
                    ->withPivot(['teacher', 'created_at', 'updated_at'])
                    ->withTimestamps();
    }
}
