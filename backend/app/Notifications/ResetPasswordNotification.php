<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token)
    {
    }

    public function via($notifiable): array
    {
        return ["mail"];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = "http://localhost:5173/reset-password?token={$this->token}&email={$notifiable->email}";

        return (new MailMessage)
            ->subject("Reset Password")
            ->line("Hai richiesto il reset della password.")
            ->action("Reset Password", $url)
            ->line("Se non hai richiesto tu il reset, ignora questa email.");
    }
}