<?php

namespace App\Actions\Users;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\User;

class UpdateUserAction extends AbstractUpdateAction
{
    protected string $modelClass = User::class;

    public function update(mixed $user, array $data): mixed
    {
        /** @var User $user */

        $data = array_filter($data, function ($value) {
            // Remove null values and empty strings
            return $value !== null && $value !== '';
        });

        $user = parent::update($user, $data);
        $user->syncRoles($data['roles']);

        return $user;
    }
}
