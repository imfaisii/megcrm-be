<?php

namespace App\Actions\Team;

use App\Actions\Common\AbstractDeleteAction;
use App\Enums\Permissions\RoleEnum;
use App\Models\Team;
use Illuminate\Support\Str;

class DeleteTeamAction extends AbstractDeleteAction
{
    protected string $modelClass = Team::class;

    public function delete($model): ?bool
    {
        $model->users()->get()->each(function ($user) {
            $teamRoles = $user?->roles?->filter(function ($role) {
                return Str::contains($role->name, 'team');
            })?->map(function ($role) {
                return $role->id;
            });
            $user->roles()->detach($teamRoles);
        });  // remove the team roles from the users
        $model->users()->detach();

        return parent::delete($model);
    }
}
