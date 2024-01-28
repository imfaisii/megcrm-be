<?php

namespace App\Actions\Permissions;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\ExtendedRole;

class UpdateRoleAction extends AbstractUpdateAction
{
    protected string $modelClass = ExtendedRole::class;

    public function update(mixed $role, array $data): mixed
    {
        $role->syncPermissions([]);
        $role->update($data);
        $role->syncPermissions($data['permissions']);

        return $role;
    }
}
