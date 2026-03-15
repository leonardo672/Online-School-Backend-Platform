<?php
// app/Services/UserNotificationService.php
namespace App\Services;

use App\Models\User;
use App\Notifications\UserCreated;

class UserNotificationService
{
    /**
     * Send welcome email to newly created user
     */
    public function sendWelcomeEmail(User $user): void
    {
        $user->notify(new UserCreated($user));
    }

    /**
     * Send profile update notification
     */
    public function sendProfileUpdateNotification(User $user): void
    {
        // You can create this notification later if needed
        // $user->notify(new UserUpdated($user));
        
        // For now, you can just log it or leave empty
        // The method exists for future use
    }
}