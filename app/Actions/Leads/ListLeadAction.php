<?php

namespace App\Actions\Leads;

use App\Actions\Common\AbstractListAction;
use App\Enums\Permissions\RoleEnum;
use App\Models\Lead;
use Spatie\QueryBuilder\QueryBuilder as SpatieQueryBuilder;
use Illuminate\Database\Eloquent\Builder;

class ListLeadAction extends AbstractListAction
{
    protected string $modelClass = Lead::class;

    public function getQuery(): SpatieQueryBuilder|Builder
    {
        $query = parent::getQuery();
        $user = auth()->user();

        if (
            !$user->hasRole(RoleEnum::SUPER_ADMIN)
            && $user->hasRole(RoleEnum::SURVEYOR)
        ) {
            $query->byRole(RoleEnum::SURVEYOR);
        }

        return $query;
    }
}
