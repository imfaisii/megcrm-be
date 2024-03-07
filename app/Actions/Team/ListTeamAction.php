<?php

namespace App\Actions\Team;

use App\Actions\Common\AbstractListAction;
use App\Enums\Permissions\RoleEnum;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;


class ListTeamAction extends AbstractListAction
{
    protected string $modelClass = Team::class;

    public function newQuery(): Builder
    {
        $query = parent::newQuery();
        $query->with([
            'users.pivot.role',
            'admin'
        ])->withCount(['users as members_count']);

        return $query;
    }
}
