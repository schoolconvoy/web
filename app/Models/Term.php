<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $casts = [
        'active' => 'boolean',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Generate academic terms: 1st, 2nd, and 3rd
     */
    public static function generateTerms()
    {
        return [
            '1st Term' => '1st Term',
            '2nd Term' => '2nd Term',
            '3rd Term' => '3rd Term',
        ];
    }

}
