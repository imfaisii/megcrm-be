<?php

namespace App\Events\Users;

use App\Models\User;

abstract class AbstractUserEvent
{
    /**
     * @param User $booking
     */
    public function __construct(
        public User $user
    ) {
    }
}
