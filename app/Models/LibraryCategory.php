<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\SessionTermSchoolScope;

#[ScopedBy([SessionTermSchoolScope::class])]
class LibraryCategory extends BaseModel
{
    use HasFactory;

    public function subcategories()
    {
        return $this->belongsToMany(LibrarySubcategory::class, 'libraries_category_subcategory', 'category', 'subcategory');
    }
}
