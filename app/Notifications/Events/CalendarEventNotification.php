<?php

namespace App\Notifications\Events;

use App\Notifications\AbstractNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class CalendarEventNotification extends AbstractNotification
{
    use Queueable;

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line($this->data['subtitle'])
            ->action('Go to calendar', config('app.frontend_url').'/calendar')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->data['title'],
            'subtitle' => $this->data['subtitle'],
            'module' => $this->data['module'],
            'redirect_link' => $this->data['link'] ?? null,
        ];
    }
}
