<?php

namespace App\Models;

use App\Models\Scopes\BaseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Session;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

class BaseModel extends Model
{
    use HasFactory, SoftDeletes;

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }

    protected static function booted(): void
    {
        parent::booted();

        static::created(function ($model) {
            $session = Session::active(auth()->user()->school_id);
            $term = $session->terms()->where('active', true)->first();

            $model->school_id = $model->school_id ?? auth()->user()->school_id;
            $model->session_id = $model->session_id ?? $session->id;
            $model->term_id = $model->term_id ?? $term->id;

            $model->save();
        });
    }
}
