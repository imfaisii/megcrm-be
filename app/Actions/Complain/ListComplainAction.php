<?php

namespace App\Actions\Complain;

use App\Actions\Common\AbstractListAction;
use App\Enums\Permissions\RoleEnum;
use App\Models\Complaints;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder as SpatieQueryBuilder;

class ListComplainAction extends AbstractListAction
{
    protected string $modelClass = Complaints::class;
}
