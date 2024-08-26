<?php

namespace App\Models;

use App\Models\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([SchoolScope::class])]
class Subject extends BaseModel
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
