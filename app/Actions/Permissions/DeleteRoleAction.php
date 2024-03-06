<?php

namespace App\Actions\Permissions;

use App\Actions\Common\AbstractDeleteAction;
use Spatie\Permission\Models\Role;

class DeleteRoleAction extends AbstractDeleteAction
{
    protected string $className = Role::class;

    public function delete($role): ?bool
    {
        /** @var Role $role */
        $role->syncPermissions([]);

        return parent::delete($role);
    }
}
