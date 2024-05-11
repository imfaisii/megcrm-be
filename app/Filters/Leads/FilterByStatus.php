<?php

namespace App\Filters\Leads;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByStatus implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->currentStatus($value);
    }
}
