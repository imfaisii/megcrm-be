<?php

namespace App\Listeners\Users;

use App\Events\Users\NewUserCreated;
use App\Notifications\Users\UserCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendUserCredentialsListener implements ShouldQueue
{
    public function handle(NewUserCreated $event): void
    {
        $event->user->notify(new UserCreatedNotification([
            ...$event->user->toArray(),
            'password' => $event->password,
        ]));
    }
}
