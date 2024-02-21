<?php

namespace App\Observers\Users;

use App\Events\Users\NewUserCreated;
use App\Models\User;
use Exception;

class UserObserver
{
    public function created(User $user): void
    {
        try {
            event(new NewUserCreated(user: $user, password: request()->get('password', '')));
        } catch (Exception $e) {
            //
        }
    }
}
