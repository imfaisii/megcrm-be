<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByCreatedAt implements Filter
{
    /**
     * @phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter
     *
     * @phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
     */
    public function __invoke(Builder $query, $value, string $property): void
    {
        if (Str::contains($value, 'to')) {
            $date = explode(' to ', $value);
            $query->whereDate('created_at', '>=', $date[0])
                ->whereDate('created_at', '<=', $date[1]);
        } else {
            $query->whereDate('created_at', $value);
        }

    }
}
