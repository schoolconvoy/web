<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

trait HasSubscriptionLimits
{
    public function exceedsClassLimit(): bool
    {
        $tenant = Tenant::find(Auth::user()->school_id);

        if (!$tenant || !$tenant->plan) {
            return true; // No valid subscription
        }

        $currentClassCount = $tenant->classes()->count();
        return $currentClassCount >= $tenant->plan->max_classes;
    }

    public function exceedsStudentLimit(): bool
    {
        $tenant = Tenant::find(Auth::user()->school_id);

        if (!$tenant || !$tenant->plan) {
            return true; // No valid subscription
        }

        $currentStudentCount = $tenant->users()->where('role', 'student')->count();
        return $currentStudentCount >= $tenant->plan->max_students;
    }

    public function exceedsTeacherLimit(): bool
    {
        $tenant = Tenant::find(Auth::user()->school_id);

        if (!$tenant || !$tenant->plan) {
            return true; // No valid subscription
        }

        $currentTeacherCount = $tenant->users()->where('role', 'teacher')->count();
        return $currentTeacherCount >= $tenant->plan->max_teachers;
    }

    public function getRemainingClasses(): int
    {
        $tenant = Tenant::find(Auth::user()->school_id);

        if (!$tenant || !$tenant->plan) {
            return 0;
        }

        $currentClassCount = $tenant->classes()->count();
        return max(0, $tenant->plan->max_classes - $currentClassCount);
    }

    public function getRemainingStudents(): int
    {
        $tenant = Tenant::find(Auth::user()->school_id);

        if (!$tenant || !$tenant->plan) {
            return 0;
        }

        $currentStudentCount = $tenant->users()->where('role', 'student')->count();
        return max(0, $tenant->plan->max_students - $currentStudentCount);
    }

    public function getRemainingTeachers(): int
    {
        $tenant = Tenant::find(Auth::user()->school_id);

        if (!$tenant || !$tenant->plan) {
            return 0;
        }

        $currentTeacherCount = $tenant->users()->where('role', 'teacher')->count();
        return max(0, $tenant->plan->max_teachers - $currentTeacherCount);
    }
}
