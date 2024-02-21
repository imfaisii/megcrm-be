<?php

namespace App\Events\Users;

use App\Models\User;

abstract class AbstractUserEvent
{
    public function __construct(
        public User $user
    ) {
    }
}
