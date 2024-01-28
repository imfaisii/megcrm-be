<?php

namespace App\Actions\Permissions;

use App\Actions\Common\AbstractDeleteAction;
use App\Actions\Common\BaseModel;
use App\Models\ExtendedRole;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DeleteRoleAction extends AbstractDeleteAction
{
    protected string $className = ExtendedRole::class;

    public function delete($role): ?bool
    {
        /** @var Role $role */
        $role->syncPermissions([]);
        return parent::delete($role);
    }
}
