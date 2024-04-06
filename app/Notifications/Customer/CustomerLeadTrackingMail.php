<?php

namespace App\Notifications\Customer;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerLeadTrackingMail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public array $params)
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
            ->line("Hi {$notifiable->title} {$notifiable->first_name} {$notifiable->last_name},")
            ->line("We hope this email finds you well!")
            ->line("We're excited to inform you that you've been granted exclusive access to review and update your details securely. Simply click the link below to access your personalized portal:")
            ->action('View',  url(config("app.CUSTOMER_URL")."/tracking/{$this->params['lead']}/{$this->params['signature']}"))
            ->line("This link will provide you with a convenient platform to review your information and upload any necessary documents. Please ensure to complete this process by ".Carbon::createFromTimestamp($this->params['time'])->format('Y-m-d H:i:s')." as the link will expire after this time.")
            ->line("Should you encounter any difficulties or have any questions, feel free to reach out to our dedicated support team .")
            ->line("Thank you for choosing".config("app.name")." We look forward to assisting you further")
            ->line("Sincerely,");
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
