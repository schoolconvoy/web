<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(LibraryCategory::class, 'category_id', 'id');
    }

    public function subcategory()
    {
        return $this->belongsTo(LibrarySubcategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function scopeDigital($query)
    {
        return $query->where('type', 'digital');
    }

    public function scopeHardCopy($query)
    {
        return $query->where('type', 'hard copy');
    }
}
