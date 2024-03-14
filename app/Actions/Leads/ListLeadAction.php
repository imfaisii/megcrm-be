<?php

namespace App\Actions\Leads;

use App\Actions\Common\AbstractListAction;
use App\Enums\Permissions\RoleEnum;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder as SpatieQueryBuilder;

class ListLeadAction extends AbstractListAction
{
    protected string $modelClass = Lead::class;

    public function getQuery(): SpatieQueryBuilder|Builder
    {
        $query = parent::getQuery();
        if (method_exists($this->modelClass, 'scopeTeamScope')) {
            $query->TeamScope(bypassRole:[RoleEnum::CSR]);
        }

        // dd($query->toRawSql());
        // $user = auth()->user();

        // if (
        //     !$user->hasRole(RoleEnum::SUPER_ADMIN)
        //     && $user->hasRole(RoleEnum::SURVEYOR)
        // ) {
        //     $query->byRole(RoleEnum::SURVEYOR);
        // }

        //  dd($query->toRawSql());
        return $query;
    }
}
