<?php

namespace App\Listeners\Users;

use App\Events\Users\NewUserCreated;
use App\Mail\Mails\Users\NewUserCredentialsMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendUserCredentialsListener implements ShouldQueue
{
    public function handle(NewUserCreated $event): void
    {
        Mail::to($event->user->email)
            ->send(new NewUserCredentialsMail($event->user->toArray()));
    }
}
