<?php

namespace App\Models;

use App\Models\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\SessionTermSchoolScope;

#[ScopedBy([SchoolScope::class])]
class Classes extends BaseModel
{
    use HasFactory, SoftDeletes;

    public function users()
    {
        return $this->hasMany(User::class, 'class_id', 'id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function teacher()
    {
        return $this->hasOne(User::class, 'id', 'teacher');
    }

    public function assistant_teacher()
    {
        return $this->hasOne(User::class, 'id', 'assistant_teacher');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'class_id', 'id');
    }

    public function fees()
    {
        return $this->belongsToMany(Fee::class);
    }

    public static function highSchool()
    {
        return self::whereIn('name', User::$HIGH_SCHOOL_CLASSES);
    }

    public static function elementarySchool()
    {
        return self::whereIn('name', User::$ELEMENTARY_SCHOOL_CLASSES);
    }
}
