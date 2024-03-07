<?php

namespace App\Notifications\Sms;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class TwilioMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected string $body)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return [TwilioChannel::class, 'database'];
    }

    public function toTwilio(object $notifiable)
    {
        $lead = $notifiable->load(['leadGenerator']);

        return (new TwilioSmsMessage())->content($this->body)->from(
            app()->isLocal()
                ? config('services.twilio.number')
                : $lead->leadGenerator->sender_id
        );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Manual SMS',
            'content' => $this->body
        ];
    }
}
