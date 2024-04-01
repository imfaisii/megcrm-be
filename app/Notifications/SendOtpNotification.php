<?php

namespace App\Notifications;

use App\Cache\TwoFactorCodeCacheClass;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class SendOtpNotification extends Notification
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
        if (app()->isProduction()) {
            if ($notifiable?->additional?->phone_no) {
                return ['mail', TwilioChannel::class];
            } else {
                return ['mail'];
            }
        } else {
            return ['mail'];
        }
    }

    public function toTwilio(object $notifiable)
    {

        $content = "The Otp for your Login is : {$this->data['code']}. please don't share with any one and use it before {$this->data['expiration_time']} seconds";
        return (new TwilioSmsMessage())->content($content)->from(config('services.twilio.number'));
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('Please Use the following Otp For Login')
            ->line("OTP: " . $this->data['code'])
            ->line("Please Don't share with anyone and remember to use it before " . $this->data['expiration_time'] . ' seconds')
            ->line("Thank you for using our application!");
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
