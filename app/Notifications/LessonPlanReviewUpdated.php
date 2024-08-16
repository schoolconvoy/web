<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class LessonPlanReviewUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public \App\Models\LessonPlanReview $review
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
        $review = $this->review;
        $lessonPlan = $review->lessonPlan;
        $lessonPlan->with('teacher');

        return (new MailMessage)
                    ->greeting('Hello ' . $notifiable->firstname . ' ' . $notifiable->lastname . '!')
                    ->line('The lesson plan **' . $lessonPlan->name . '** in '
                            . $lessonPlan->subject->name .
                            ' for the class **' . $lessonPlan->class->name .
                            '** has new been updated by '
                            . $lessonPlan->teacher?->firstname
                            . ' ' . $lessonPlan->teacher?->lastname . ':'
                        )
                    ->line("They addressed your comment which says:")
                    ->line("**".$review->comment."**")
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
