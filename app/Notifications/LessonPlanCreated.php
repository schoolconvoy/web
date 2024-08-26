<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LessonPlanCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public \App\Models\LessonPlan $lessonPlan
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
        $lessonPlan = $this->lessonPlan;

        return (new MailMessage)
                    ->subject('New lesson plan created')
                    ->greeting('Hello ' . $notifiable->firstname . ' ' . $notifiable->lastname . '!')
                    ->line('**Lesson Plan:** ' . $lessonPlan->name)
                    ->line('**Subject:** ' . $lessonPlan->subject->name)
                    ->line('**Class:** ' . $lessonPlan->class->name)
                    ->line('**Teacher:** ' . $lessonPlan->teacher->firstname . ' ' . $lessonPlan->teacher->lastname)
                    ->action('View lesson plan', route('filament.admin.resources.lesson-plans.view-lesson', ['record' => $lessonPlan->week->id, 'plan' => $lessonPlan->id]))
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
