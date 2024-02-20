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
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->view(
            'mails.users.new-user-credentials',
            ['data' => $this->data]
        );
    }
}
