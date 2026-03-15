<?php
// app/Notifications/UserCreated.php
namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UserCreated extends Notification
{
    use Queueable;

    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Welcome to our platform!')
                    ->greeting('Hello ' . $this->user->name . '!')
                    ->line('Your account has been created successfully.')
                    ->action('Login', url('/login'))
                    ->line('Thank you for joining us!');
    }

    public function toArray($notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'message' => 'Your account has been created successfully.',
            'type' => 'user_created'
        ];
    }
}