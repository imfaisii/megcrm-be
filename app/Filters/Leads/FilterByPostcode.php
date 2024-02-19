<?php

namespace App\Filters\Leads;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByPostcode implements Filter
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
        $query->where(DB::raw("REPLACE(post_code, ' ', '')"), 'like', '%' . str_replace(' ', '', $value) . '%');
    }
}
