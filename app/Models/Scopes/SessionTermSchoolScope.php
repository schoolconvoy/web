<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Models\Session;
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
            if (!session()->has('currentSession') || !session()->has('currentTerm')) {
                $activeSession = Session::active(auth()->user()->school_id);
                $activeTerm = $activeSession->terms()->where('active', true)->first();

                session()->put('currentSession', $activeSession);
                session()->put('currentTerm', $activeTerm);
            } else {
                $activeSession = session()->get('currentSession');
                $activeTerm = session()->get('currentTerm');
            }

            $school_id = session()->get('school_id') ? session()->get('school_id') : auth()->user()->school_id;
            $table = $model->getTable();

            // Store the current route in session
            $currentRoute = request()->route()->getName();
            $currentRouteParams = request()->route()->parameters();

            $builder->where($table.'.session_id', $activeSession->id)
                    ->where($table.'.term_id', $activeTerm->id)
                    ->where($table.'.school_id', $school_id);
        }
    }
}
