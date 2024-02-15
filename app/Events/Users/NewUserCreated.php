<?php

namespace App\Events\Users;

use App\Models\User;

class NewUserCreated extends AbstractUserEvent
{
    public function __construct(public User $user, public string $password)
    {
        //
    }
}
