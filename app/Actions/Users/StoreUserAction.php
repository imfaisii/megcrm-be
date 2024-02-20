<?php

namespace App\Actions\Users;

use App\Actions\Common\AbstractCreateAction;
use App\Enums\Permissions\RoleEnum;
use App\Models\Surveyor;
use App\Models\User;
use Spatie\Permission\Models\Role;

class StoreUserAction extends AbstractCreateAction
{
    protected string $modelClass = User::class;

    public function create(array $data): User
    {
        /** @var User $user */
        $data['created_by_id'] = auth()->id();

        $user = parent::create($data);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        $surveyorRole = Role::where('name', RoleEnum::SURVEYOR)->first();

        if (in_array($surveyorRole->id, $data['roles'])) {
            Surveyor::create([
                'user_id' => $user->id
            ]);
        }

        return $user;
    }
}
