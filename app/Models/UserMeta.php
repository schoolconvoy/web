<?php

namespace App\Models;

use App\Filament\Resources\StudentResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class UserMeta extends Model
{
    use HasFactory;

    protected $casts = [
        'value' => 'json',
    ];

    public function user ()
    {
        return $this->belongsTo(User::class);
    }

    public function admission_no()
    {
        return $this->where('key', StudentResource::STUDENT_ADMISSION_NO)->first();
    }

    public static function getMeta($key)
    {
        $meta = self::where('key', $key)->first();

        if (!$meta)
        {
            Log::debug('[MISSING_USER_META] The key ' . $key . ' does not exist');
        }

        return $meta->value ?? null;
    }
}
