<?php

namespace App\Actions\Customer;

use App\Actions\Common\AbstractFindAction;
use App\Actions\Common\AbstractListAction;
use App\Enums\Permissions\RoleEnum;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder as SpatieQueryBuilder;

class ListCustomerLeadStatusAction extends AbstractFindAction
{
    protected string $modelClass = Lead::class;
}
