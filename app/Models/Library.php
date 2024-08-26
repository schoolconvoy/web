<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\SessionTermSchoolScope;

#[ScopedBy([SessionTermSchoolScope::class])]
class Library extends BaseModel
{
    use HasFactory;

    protected $casts = [
        'file' => 'json',
    ];

    public function category()
    {
        return $this->belongsTo(LibraryCategory::class, 'category_id', 'id');
    }

    public function subcategory()
    {
        return $this->belongsTo(LibrarySubcategory::class, 'subcategory_id', 'id');
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
