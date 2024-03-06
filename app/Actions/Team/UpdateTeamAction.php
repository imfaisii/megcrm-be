<?php

namespace App\Actions\Team;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\Team;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;

class UpdateTeamAction extends AbstractUpdateAction
{
    protected string $modelClass = Team::class;

    public function update(mixed $team, array $data): mixed
    {
        $team = parent::update($team, Arr::only($data, ['name', 'admin_id']));

        $preapredArray = [];
        foreach ($data['members'] as $user) {
            $preapredArray[$user] = ['role_id' => 11];   // setting the team
        }
        $preapredArray[$data['admin_id']] = ['role_id' => 12]; // setting the admin
        $team->users()->sync($preapredArray);


        return $team;
    }
}
