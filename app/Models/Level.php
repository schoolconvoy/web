<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
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
