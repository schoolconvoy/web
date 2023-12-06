<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Subject extends Model
{
    use HasFactory;

    public function level()
    {
        return $this->belongsToMany(Level::class)
                    ->withPivot(['teacher', 'created_at', 'updated_at'])
                    ->withTimestamps();
    }

    public function teacher()
    {
        return $this->hasOne(User::class);
    }

    public static function unassigned($id)
    {
        $subjects =  self::whereDoesntHave('level', function ($query) use ($id) {
            $query->where('id', $id);
        })->get();

        return $subjects;
    }
}
