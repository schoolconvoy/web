<?php

namespace App\Notifications;

use App\Listeners\StudentAttendanceListener;
use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceAlert extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Attendance $attendance
    )
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $status = $this->attendance->status;
        $late = $status === Attendance::LATE;
        $absent = $status === Attendance::ABSENT;
        $statusToWard = $late ? 'late' : 'absent';

        $ward = $this->attendance->students;
        $parent = $this->attendance->students->parent()->first();
        $salutation = $parent->gender === 'male' ? 'Mr.' : 'Mrs.';

        return (new MailMessage)
                    ->line('Dear ' . $salutation . ' '  . $parent->lastname)
                    ->line('We are sending this email to inform you that your child, ')
                    ->line($ward->firstname . ($late ? ' arrived late to school today.' : ' was not present in school today.'))
                    ->line('We take students punctuality seriously and that counts towards their end of term results.')
                    ->line('We will love if you could help them get to school earlier subsequently')
                    ->line('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
