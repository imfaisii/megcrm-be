<?php

namespace App\Filters\Users;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByRole implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->whereDoesntHave('roles')
            ->orWhereHas('roles', function ($query) use ($value) {
                return $query->where('name', '!=', $value);
            });
    }
}
