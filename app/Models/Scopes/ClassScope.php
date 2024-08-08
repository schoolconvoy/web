<?php

namespace App\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ClassScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::hasUser()) {
            if (Auth::user()->hasAnyRole([
                User::$HIGH_PRINCIPAL_ROLE,
                User::$ELEM_PRINCIPAL_ROLE,
                User::$TEACHER_ROLE,
                User::$PART_TIME_TEACHER_ROLE,
                User::$SUBSTITUTE_TEACHER_ROLE,
                User::$CORPER_ROLE,
                User::$ASST_TEACHER_ROLE
            ]))
            {
                if (Auth::user()->isHighSchool())
                {
                    $builder->highSchool();
                }
                else
                {
                    $builder->elementarySchool();
                }
            }
        }
    }
}
