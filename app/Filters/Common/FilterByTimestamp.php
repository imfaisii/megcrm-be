<?php

namespace App\Filters\Common;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByTimestamp implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $property = $property == 'timestamp' ? 'created_at' : $property;

        if (!Str::contains($value, ' to')) {
            $query->whereDate($property, $value);
        } else {
            [$from, $to] = explode(' to ', $value);

            $date_from = Carbon::parse($from)->startOfDay();
            $date_to = Carbon::parse($to)->endOfDay();

            $query->whereBetween($property, [$date_from, $date_to]);
        }
    }
}
