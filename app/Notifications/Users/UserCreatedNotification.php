<?php

namespace App\Notifications\Users;

use App\Notifications\AbstractNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class UserCreatedNotification extends AbstractNotification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('Email: '.$this->data['email'])
            ->line('Password: '.$this->data['password'])
            ->action('Go to website', config('app.frontend_url').'/login')
            ->line('Thank you for using our application!');
    }
}
