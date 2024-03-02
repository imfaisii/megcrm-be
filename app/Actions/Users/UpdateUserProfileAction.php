<?php

namespace App\Actions\Users;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\User;

class UpdateUserProfileAction extends AbstractUpdateAction
{
    protected string $modelClass = User::class;
}
