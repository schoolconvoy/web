<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    use HasFactory;

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'classes_fee');
    }

    public function category()
    {
        return $this->belongsTo(FeeCategory::class, 'fee_category', 'id');
    }
}
