<?php

namespace App\Providers;

use App\Events\CreatedUser;
use App\Events\PaymentReceived;
use App\Listeners\SendPaymentReceipt;
use App\Events\StudentCreatedEvent;
use App\Events\StudentAttendance;
use App\Events\StudentIsLate;
use App\Listeners\SendWelcomeNotification;
use App\Listeners\StudentAttendanceListener;
use App\Models\Attendance;
use App\Models\User;
use App\Observers\AttendanceObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Observers\UserObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        PaymentReceived::class => [
            SendPaymentReceipt::class
        ],
        StudentAttendance::class => [
            StudentAttendanceListener::class
        ],
        CreatedUser::class => [
            SendWelcomeNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Attendance::observe(AttendanceObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
