<?php

namespace App\Filters\Leads;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByStatus implements Filter
{
    /**
     * @param Builder $query
     * @param $value
     * @param string $property
     * @return void
     * @phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter
     * @phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
     */
    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->currentStatus($value);
    }
}
