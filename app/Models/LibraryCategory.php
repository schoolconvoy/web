<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryCategory extends Model
{
    use HasFactory;

    public function subcategories()
    {
        return $this->belongsToMany(LibrarySubcategory::class, 'libraries_category_subcategory', 'category', 'subcategory');
    }
}
