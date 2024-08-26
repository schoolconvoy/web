<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class UserRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public $url,
        public User $user
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
        $roleName = $this->user->roles[0]->name ?? User::$STUDENT_ROLE;

        $name = $this->user->fullname;

        if ($roleName === User::$PARENT_ROLE)
        {
            $action = 'do things like see your wards results, pay fees, etc.';
        }
        else if($roleName === User::$TEACHER_ROLE)
        {
            $action = 'do things like manage your class, grade students, add quizzes.';
        }
        else if($roleName === User::$STUDENT_ROLE)
        {
            $action = 'do things like see your results, take quizzes, etc.';
        }
        else
        {
            $action = 'access your customized dashboard.';
        }

        return (new MailMessage)
            ->subject('Hi ' . $name)
            ->line('You are receiving this email because you have been added to the Interguide Academy school portal as a ' . $roleName . '.')
            ->line('Please use the following link to set your password.')
            ->line('After setting your password you will be able to ' . $action)
            ->action('Set Password', $this->url)
            ->line('For security reasons this password reset link can be used only once, and will expire in 72 hours.');
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
