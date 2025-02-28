<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Classes;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;

class SubscriptionServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Listen for user registration events to check subscription limits
        Event::listen(Registered::class, function ($event) {
            $user = $event->user;
            $tenant = Tenant::find($user->school_id);

            if (!$tenant || !$tenant->subscription || !$tenant->subscription->isActive()) {
                throw new \Exception('Please subscribe to a plan to add new users.');
            }

            if ($user->role === 'student' && $tenant->exceedsStudentLimit()) {
                throw new \Exception('You have reached the maximum number of students allowed in your subscription plan.');
            }

            if ($user->role === 'teacher' && $tenant->exceedsTeacherLimit()) {
                throw new \Exception('You have reached the maximum number of teachers allowed in your subscription plan.');
            }
        });

        // Add subscription-related macros to models
        Classes::macro('canAddMore', function () {
            return !$this->exceedsClassLimit();
        });

        User::macro('canAddMoreStudents', function () {
            return !$this->exceedsStudentLimit();
        });

        User::macro('canAddMoreTeachers', function () {
            return !$this->exceedsTeacherLimit();
        });
    }
}
