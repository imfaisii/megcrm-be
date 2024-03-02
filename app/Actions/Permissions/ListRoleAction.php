<?php

namespace App\Actions\Permissions;

use App\Actions\Common\AbstractListAction;
use App\Models\ExtendedRole;

class ListRoleAction extends AbstractListAction
{
    protected string $modelClass = ExtendedRole::class;
}
