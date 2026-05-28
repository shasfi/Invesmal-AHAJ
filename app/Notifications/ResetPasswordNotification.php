<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
            'role' => $notifiable->role,
        ], false));

        return (new MailMessage)
            ->subject('Reset your Invesmal password')
            ->line('You requested a password reset. Click the button below to choose a new password.')
            ->action('Reset password', $url)
            ->line('This link expires in 60 minutes. If you did not request this, ignore this email.');
    }
}
