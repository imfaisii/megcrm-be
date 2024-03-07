<?php

namespace App\Actions\Team;

use App\Actions\Common\AbstractCreateAction;
use App\Actions\Common\BaseModel;
use App\Enums\Permissions\RoleEnum;
use App\Models\Team;
use App\Models\User;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

class StoreTeamAction extends AbstractCreateAction
{
    protected string $modelClass = Team::class;


    public function create(array $data): Team|BaseModel
    {
        $team = DB::transaction(function () use ($data) {
            /** @var User $user */
            $data['created_by_id'] = auth()->id();

            $team = parent::create(Arr::only($data, ['name', 'created_by_id', 'admin_id']));
            if ($team) {
                // // team created successfully, now add members with roles
                $team->users()->attach($data['members'], [
                    'role_id' => Cache::rememberForever('team_admin_member', function () {
                        return Role::findByName(RoleEnum::TEAM_MEMBER);
                    })?->id
                ]);      // setting the other memebers
                $team->users()->attach([
                    $data['admin_id'] => [
                        'role_id' => Cache::rememberForever('team_admin_role', function () {
                            return Role::findByName(RoleEnum::TEAM_ADMIN);
                        })?->id
                    ]
                ]);    // setting the admin for the team

            }
            return $team;
        });

        return $team;
    }

}
