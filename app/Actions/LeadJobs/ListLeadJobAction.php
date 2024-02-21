<?php

namespace App\Actions\LeadJobs;

use App\Actions\Common\AbstractListAction;
use App\Enums\Permissions\RoleEnum;
use App\Models\Lead;
use Spatie\QueryBuilder\QueryBuilder as SpatieQueryBuilder;
use Illuminate\Database\Eloquent\Builder;

class ListLeadJobAction extends AbstractListAction
{
    protected string $modelClass = Lead::class;

    public function getQuery(): SpatieQueryBuilder|Builder
    {
        $query = parent::getQuery();
        $user = auth()->user();

        $query->where('is_marked_as_job', true);

        if (
            !$user->hasRole(RoleEnum::SUPER_ADMIN)
            && $user->hasRole(RoleEnum::SURVEYOR)
        ) {
            $query->byRole(RoleEnum::SURVEYOR);
        }

        return $query;
    }
}
