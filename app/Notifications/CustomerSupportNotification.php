<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerSupportNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public array $data)
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

        return (new MailMessage)
            ->subject('Customer Support Notification')
            ->line('Dear Customer Support,')
            ->line('A new customer support request has been received:')
            ->line('Lead Reference Number: ' . $this->data['lead_reference_number'])
            ->line('Customer Name: ' . $this->data['customer_name'])
            ->line('Post Code: ' . $this->data['post_code'])
            ->line('Address: ' . $this->data['address'])
            ->line('Content: ' . $this->data['content'])
            ->action('View Lead', url(config('app.frontend_url') . '/leads/edit/' . $this->data['lead_id'] . '/customer-details'))
            ->line('Thank you');
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
