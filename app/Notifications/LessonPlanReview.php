<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Events\LessonPlanReviewed;

class LessonPlanReview extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public LessonPlanReviewed $event
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
        $review = $this->event->review;
        $lessonPlan = $review->lessonPlan;

        return (new MailMessage)
                    ->greeting('Hello ' . $notifiable->firstname . ' ' . $notifiable->lastname . '!')
                    ->line('Your lesson plan **' . $lessonPlan->name . '** in '. $lessonPlan->subject->name . ' for the class **' . $lessonPlan->class->name .'** has new updates:')
                    ->line("> " . $review->comment)
                    ->line('Please review the changes and make necessary updates.')
                    ->action('View lesson plan', route('filament.admin.resources.lesson-plans.view-lesson', ['record' => $lessonPlan->week->id, 'plan' => $lessonPlan->id]) . "#lesson-plan-review")
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
