<?php

namespace App\Actions\Surveyors;

use App\Actions\Common\AbstractCreateAction;
use App\Actions\Users\StoreUserAction;
use App\Enums\Permissions\RoleEnum;
use App\Models\Surveyor;
use Spatie\Permission\Models\Role;

class StoreSurveyorAction extends AbstractCreateAction
{
    protected string $modelClass = Surveyor::class;

    public function create(array $data): Surveyor
    {
        $user = (new StoreUserAction())->create($data);

        $user->assignRole(Role::where('name', RoleEnum::SURVEYOR)->first());

        return $user->surveyor()->firstOrCreate([
            'created_by_id' => auth()->id()
        ]);
    }
}
