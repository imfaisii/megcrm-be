<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\ExpoMessage;

class TextExponentNotification extends Notification
{
    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return [ExpoChannel::class];
    }

    public function toExpoPush($notifiable)
    {
        return ExpoMessage::create()
            ->badge(1)
            ->enableSound()
            ->title("Congratulations!")
            ->body("You have working push notifications account was approved!");
    }
}
