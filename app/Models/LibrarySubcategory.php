<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\SessionTermSchoolScope;

#[ScopedBy([SessionTermSchoolScope::class])]
class LibrarySubcategory extends BaseModel
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(LibraryCategory::class);
    }
}
