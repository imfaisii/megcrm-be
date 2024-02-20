<?php

namespace App\Actions\Surveyors;

use App\Actions\Common\AbstractCreateAction;
use App\Actions\Users\StoreUserAction;
use App\Enums\Permissions\RoleEnum;
use App\Models\User;

class StoreSurveyorAction extends AbstractCreateAction
{
    public function create(array $data): User
    {
        $user = (new StoreUserAction())->create($data);

        $user->assignRole(RoleEnum::SURVEYOR);

        return $user;
    }
}
