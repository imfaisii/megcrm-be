<?php

namespace App\Actions\Surveyors;

use App\Actions\Common\AbstractListAction;
use App\Enums\Permissions\RoleEnum;
use App\Models\Surveyor;
use Illuminate\Database\Eloquent\Builder;


class ListSurveyorAction extends AbstractListAction
{
    protected string $modelClass = Surveyor::class;

    public function newQuery(): Builder
    {
        $query = parent::newQuery();

        $query->whereHas('user', function ($query) {
            $query->whereHas('roles', function ($query) {
                $query->where('name', RoleEnum::SURVEYOR);
            });
        })
            ->with('user');

        return $query;
    }
}
