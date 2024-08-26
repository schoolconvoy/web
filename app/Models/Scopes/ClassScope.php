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
     * This scope is used to only show people information related to their classes
     * Which is in turn an hack for separating the two schools from each other.
     * We have to take into consideration the fact that a school can have both
     * high school and elementary schools and the system has to interpret that as
     * two different schools. But with a common parent school.
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
