<?php

namespace App\Actions\Permissions;

use App\Actions\Common\AbstractCreateAction;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class StoreRoleAction extends AbstractCreateAction
{
    protected string $modelClass = Role::class;

    public function create(array $data): Role
    {
        /** @var Role $role */
        $role = parent::create($data);
        $authPermissions = Permission::where('parent_module_name', 'auth')->pluck('id');

        $permissions = [...$authPermissions];

        if (array_key_exists('permissions', $data)) {
            $permissions = [...$permissions, ...$data['permissions']];
        }

        $role->syncPermissions($permissions);

        return $role;
    }
}
