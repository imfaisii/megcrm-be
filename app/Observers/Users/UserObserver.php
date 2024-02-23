<?php

namespace App\Observers\Users;

use App\Events\Users\NewUserCreated;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        event(new NewUserCreated(user: $user, password: request()->get('password', '')));
    }
}
