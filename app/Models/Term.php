<?php

namespace App\Models;

use App\Models\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\SessionTermSchoolScope;

class Term extends BaseModel
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
