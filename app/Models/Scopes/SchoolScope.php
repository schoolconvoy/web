<?php

namespace App\Models\Scopes;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SchoolScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        Log::debug("Model is " . get_class($model));

       if (Auth::hasUser()) {
            $user = Auth::user();
            $table = $model->getTable();

           if (session()->has('school_id')) {
                $school_id = session()->get('school_id');
           } else {
               $school_id = School::find($user->school_id);
               session()->put('school_id', $school_id->id);
           }

        //    Log::debug('SessionTermSchoolScope: '.$table);
        //    Log::debug('school_id: '.$school_id);
        //    Log::debug('user: '.$user);

           $builder->where($table.'.school_id', $school_id);
       }
    }
}
