<?php

namespace App\Filters\Common;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByTimestamp implements Filter
{
    /**
     * @phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter
     *
     * @phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
     */
    public function __invoke(Builder $query, $value, string $property): void
    {
        if (! Str::contains($value, ' to')) {
            $query->whereDate('created_at', $value);
        } else {
            [$from, $to] = explode(' to', $value);

            $query->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59']);
        }
    }
}
