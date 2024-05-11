<?php

namespace App\Filters\CalendarEvents;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByCalendars implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        if (! is_array($value)) {
            $value = [$value];
        }

        $query->whereIn('calendar_id', $value);
    }
}
