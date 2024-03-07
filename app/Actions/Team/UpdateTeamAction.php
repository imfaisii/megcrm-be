<?php

namespace App\Actions\Team;

use App\Actions\Common\AbstractUpdateAction;
use App\Enums\Permissions\RoleEnum;
use App\Models\Team;
use DB;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

class UpdateTeamAction extends AbstractUpdateAction
{
    protected string $modelClass = Team::class;

    public function update(mixed $team, array $data): mixed
    {
        try {
            return $result = DB::transaction(function () use ($team, $data) {
                $team = parent::update($team, Arr::only($data, ['name', 'admin_id']));

                $preapredArray = [];
                foreach ($data['members'] as $user) {
                    $preapredArray[$user] = [
                        'role_id' => Cache::rememberForever('team_admin_member', function () {
                            return Role::findByName(RoleEnum::TEAM_MEMBER);
                        })?->id
                    ];   // setting the team
                }
                $preapredArray[$data['admin_id']] = [
                    'role_id' => Cache::rememberForever('team_admin_role', function () {
                        return Role::findByName(RoleEnum::TEAM_ADMIN);
                    })?->id
                ]; // setting the admin
                $team->users()->sync($preapredArray);


                return $team;
            });
        } catch (Exception $e) {
            return $team;
        }

    }
}
