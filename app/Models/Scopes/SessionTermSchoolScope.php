<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Models\Session;
use App\Models\Term;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SessionTermSchoolScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::hasUser()) {
            // Copied from app/Livewire/SessionAndTermPicker.php
            // TODO: Refactor this to a service class
            if (session()->has('currentSession')) {
                $activeSession = session()->get('currentSession');
            } else {
                $activeSession = Session::active(auth()->user()->school_id);
            }

            if (!$activeSession) {
                $activeSession = Session::active(auth()->user()->school_id);
            }

            if (session()->has('currentTerm')) {
                $activeTerm = session()->get('currentTerm');
            } else if (!is_null($activeSession)) {
                $activeTerm =  $activeSession->terms()->where('active', true)->first();
            } else {
                $activeTerm = Term::where('school_id', auth()->user()->school_id)
                                        ->where('active', true)
                                        ->first();
            }

            session()->put('currentSession', $activeSession);
            session()->put('currentTerm', $activeTerm);

            $school_id = session()->get('school_id') ? session()->get('school_id') : auth()->user()->school_id;
            $table = $model->getTable();

            $builder->where($table.'.session_id', $activeSession->id ?? null)
                    ->where($table.'.term_id', $activeTerm->id ?? null)
                    ->where($table.'.school_id', $school_id);
        }
    }
}
