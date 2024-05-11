<?php

namespace App\Filters\Leads;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByLeadsPageButtonOnApp implements Filter
{
    const TODAY = 'Today';
    const WEEKLY = 'Weekly';
    const MONTHLY = 'Weekly';

    public function __invoke(Builder $query, $value, string $property): void
    {
        match ($value) {
            self::TODAY => $query->whereHas('surveyBooking', function ($query) {
                $today = Carbon::today();
                $query->whereDate('survey_at', $today);
            }),
            self::WEEKLY => $query->whereHas('surveyBooking', function ($query) {
                $startLastWeek = Carbon::now()->startOfWeek();
                $endLastWeek = $startLastWeek->copy()->endOfWeek();
                $query->whereDate('survey_at', '>=', $startLastWeek)
                    ->whereDate('survey_at', '<=', $endLastWeek);
            }),
            self::MONTHLY => $query->whereHas('surveyBooking', function ($query) {
                $startThisMonth = Carbon::now()->startOfMonth();
                $endThisMonth = $startThisMonth->copy()->endOfMonth();
                $query->whereDate('survey_at', '>=', $startThisMonth)
                    ->whereDate('survey_at', '<=', $endThisMonth);
            }),
            default => $query,
        };
    }
}
