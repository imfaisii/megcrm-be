<?php

namespace App\Includes\Users;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Includes\IncludeInterface;

class UserNotificationsInclude implements IncludeInterface
{
    /**
     * @param Builder $query
     * @param $value
     * @return void
     * @phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter
     * @phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
     */
    public function __invoke(Builder $query, $include): void
    {
        dd($include);
    }
}
