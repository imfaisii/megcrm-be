<?php

namespace App\Filters\Users;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByGivenRole implements Filter
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
        if (!is_array($value)) {
            $value = [$value];
        }

        $query->whereHas('roles', function ($query) use ($value) {
            return $query->whereIn('id', $value);
        });
    }
}
