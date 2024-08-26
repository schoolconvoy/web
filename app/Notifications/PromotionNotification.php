<?php

namespace App\Notifications;

use App\Models\Classes;
use App\Models\Promotion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PromotionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Classes $oldClass,
        public Classes $newClass,
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
        $oldClass = $this->oldClass;
        $newClass = $this->newClass;

        return (new MailMessage)
                    ->subject('Congratulations! You have been promoted.')
                    ->greeting("Congratulations {$notifiable->firstname}!")
                    ->line("You have been promoted from {$oldClass->name} to {$newClass->name}.")
                    ->line("You are now in class {$newClass->level->name}.")
                    ->line("We wish you all the best in your new class.")
                    ->line("You can now access the new class materials.")
                    ->action('Go to your new class', route('filament.student.pages.dashboard'));
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
