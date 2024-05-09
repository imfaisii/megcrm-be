<?php

namespace App\Filters\Leads;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByPostcode implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        if (is_array($value)) {
            $value = implode(",", $value);
        }

        $query->where(DB::raw("REPLACE(post_code, ' ', '')"), 'like', '%' . str_replace(' ', '', $value) . '%');
    }
}
