<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Generate academic sessions for the next 10 years
     */
    public static function generateSessions()
    {
        $sessions = [];
        $year = date('Y') - 3;

        for ($i = 0; $i < 20; $i++) {

            $sessions[$year . '/' . substr($year + 1, 2)] = $year . '/' . substr($year + 1, 2);

            $year++;
        }

        return $sessions;
    }

    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    public static function active()
    {
        return self::where('active', true)->first();
    }
}
