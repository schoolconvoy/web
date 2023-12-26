<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public function fees()
    {
        return $this->belongsToMany(Fee::class, 'fee_payments');
    }
}
