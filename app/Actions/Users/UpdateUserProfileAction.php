<?php

namespace App\Actions\Users;

use App\Actions\Common\AbstractUpdateAction;
use App\Actions\Common\BaseModel;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UpdateUserProfileAction extends AbstractUpdateAction
{
    protected string $modelClass = User::class;
}
