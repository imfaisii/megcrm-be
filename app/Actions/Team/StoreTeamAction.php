<?php

namespace App\Actions\Team;

use App\Actions\Common\AbstractCreateAction;
use App\Actions\Common\BaseModel;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class StoreTeamAction extends AbstractCreateAction
{
    protected string $modelClass = Team::class;


    public function create(array $data): Team|BaseModel
    {
        /** @var User $user */
        $data['created_by_id'] = auth()->id();

        $team = parent::create(Arr::only($data, ['name', 'created_by_id', 'admin_id']));
        if ($team) {
            // // team created successfully, now add members with roles
            $team->users()->attach($data['members'], ['role_id' => 12]);      // setting the other memebers
            $team->users()->attach([$data['admin_id'] => ['role_id' => 11]]);    // setting the admin for the team

        }
        return $team;
    }

}
