<?php

namespace App\Actions\Permissions;

use App\Actions\Common\AbstractListAction;
use App\Models\ExtendedPermission;

class ListPermissionsAction extends AbstractListAction
{
    protected string $modelClass = ExtendedPermission::class;
}
