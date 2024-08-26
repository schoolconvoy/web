<?php

namespace App\Models;

use App\Models\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

class Session extends BaseModel
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
        $existingYears = Session::all()->pluck('year')->toArray();

        $sessions = [];
        $year = date('Y') - 5;

        for ($i = 0; $i < 50; $i++) {

            $sessions[$year . '/' . substr($year + 1, 2)] = $year . '/' . substr($year + 1, 2);

            $year++;
        }

        $newYears = array_diff($sessions, $existingYears);

        return $newYears;
    }

    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    public static function active($school_id)
    {
        return self::where('active', true)
                    ->where('school_id', $school_id)
                    ->first();
    }
}
