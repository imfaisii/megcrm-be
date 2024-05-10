<?php

namespace App\Notifications\MobileApp;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\ExpoMessage;

class ExpoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected string $title, protected string $body)
    {
        //
    }

    public function via($notifiable): array
    {
        return [ExpoChannel::class];
    }

    public function toExpoPush($notifiable): ExpoMessage
    {
        return ExpoMessage::create()
            ->enableSound()
            ->title($this->title)
            ->body($this->body);
    }
}
