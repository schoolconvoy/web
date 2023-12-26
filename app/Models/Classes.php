<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classes extends Model
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
}
