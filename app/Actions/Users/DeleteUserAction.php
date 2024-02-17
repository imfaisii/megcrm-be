<?php

namespace App\Actions\Users;

use App\Actions\Common\AbstractDeleteAction;
use App\Models\User;

class DeleteUserAction extends AbstractDeleteAction
{
    protected string $modelClass = User::class;

    public function delete($model): ?bool
    {
        $model->syncRoles([]);

        return parent::delete($model);
    }
}
