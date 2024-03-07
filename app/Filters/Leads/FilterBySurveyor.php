<?php

namespace App\Filters\Leads;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FilterBySurveyor implements Filter
{
    /**
     * @phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter
     *
     * @phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
     */
    public function __invoke(Builder $query, $value, string $property): void
    {
        if (! is_array($value)) {
            $value = [$value];
        }

        $query->whereHas('surveyBooking', function ($query) use ($value) {
            $query->whereIn('surveyor_id', $value);
        });
    }
}
