<?php

namespace App\Actions\Surveyors;

use App\Actions\Common\AbstractListAction;
use App\Enums\Permissions\RoleEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;


class ListSurveyorAction extends AbstractListAction
{
    protected string $modelClass = User::class;

    public function newQuery(): Builder
    {
        $query = parent::newQuery();

        $query->whereHas('roles', function ($query) {
            $query->where('name', RoleEnum::SURVEYOR);
        });

        return $query;
    }
}
