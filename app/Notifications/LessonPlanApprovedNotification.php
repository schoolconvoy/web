<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use App\Events\LessonPlanApproved;

class LessonPlanApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public LessonPlanApproved $event
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
        $lessonPlan = $this->event->lessonPlan;

        return (new MailMessage)
                    ->greeting('Hello ' . $notifiable->firstname . ' ' . $notifiable->lastname . '!')
                    ->line('Your lesson plan **' . $lessonPlan->name . '** in '. $lessonPlan->subject->name . ' for the class **' . $lessonPlan->class->name .'** has been approved.')
                    ->action('View lesson', route('filament.admin.resources.lesson-plans.view-lesson', ['record' => $lessonPlan->week->id, 'plan' => $lessonPlan->id]))
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
