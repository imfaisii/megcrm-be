<?php

namespace App\Filters\Leads;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByName implements Filter
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
        $names = explode(' ', $value);

        if (count($names) == 2) {
            $firstPart = $names[0];
            $lastPart = $names[1];

            $query->where('first_name', 'like', '%' . $firstPart . '%')
                ->orWhere('last_name', 'like', '%' . $lastPart . '%');
        } else {
            $query->where('first_name', 'like', '%' . $value . '%')
                ->orWhere('last_name', 'like', '%' . $value . '%');
        }
    }
}
