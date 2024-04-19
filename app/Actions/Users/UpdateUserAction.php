<?php

namespace App\Actions\Users;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UpdateUserAction extends AbstractUpdateAction
{
    protected string $modelClass = User::class;

    public function update(mixed $user, array $data): mixed
    {
        $user = parent::update($user, $data);

        $oldRoles = $user->roles()->pluck('name');

        // adding roles
        $user->syncRoles($data['roles']);

        $newRoles = Role::whereIn('id', $data['roles'])->pluck('name');

        if ($newRoles != $oldRoles) {
            $attributes = [];
            $old = [];

            if ($newRoles != $oldRoles) {
                $attributes['roles'] = $newRoles;
                $old['roles'] = $oldRoles;
            }

            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->withProperties([
                    'attributes' => $attributes,
                    'old' => $old,
                ])
                ->event('updated')
                ->log('This record has been updated');
        }

        // update or create relations
        (new StoreUserAction())->relations($user, $data);

        return $user;
    }
}
