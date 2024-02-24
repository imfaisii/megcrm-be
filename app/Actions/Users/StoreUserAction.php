<?php

namespace App\Actions\Users;

use App\Actions\Common\AbstractCreateAction;
use App\Enums\Permissions\RoleEnum;
use App\Models\User;

class StoreUserAction extends AbstractCreateAction
{
    protected string $modelClass = User::class;

    protected $relations = ['additional'];

    public function create(array $data): User
    {
        /** @var User $user */
        $data['created_by_id'] = auth()->id();

        $user = parent::create($data);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        $this->relations($user, $data);

        return $user;
    }

    public function relations(User $user, array $data): void
    {
        foreach ($this->relations as $key => $relation) {
            $user->additional()->updateOrCreate([
                'user_id' => $user->id
            ], $data[$relation]);
        }
    }
}
