<?php

namespace App\Filters\Team;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByAdminName implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->whereHas('admin', function ($query) use ($value) {
            $query->where('name', 'like', "%{$value}%");
        });

    }
}
