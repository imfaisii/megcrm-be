<?php

namespace App\Filters\Leads;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByFeatures implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        if (!is_array($value)) {
            $value = [$value];
        }


        $query->whereRaw("JSON_SEARCH(LOWER(epc_details), 'one', LOWER('%{$value[0]}%'), NULL, '$.features[*].description') IS NOT NULL");
    }
}
