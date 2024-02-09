<?php

namespace App\Filters\Leads;

use App\Imports\Leads\LeadsImport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByName implements Filter
{
    /**
     * @param Builder $query
     * @param $value
     * @param string $property
     * @return void
     * @phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter
     * @phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
     */
    public function __invoke(Builder $query, $value, string $property): void
    {
        if (Str::contains($value, " ")) {
            $name = (new LeadsImport)->split_name($value);

            $query->where('first_name', 'like', '%' . $name['first_name'] . '%')
                ->when($name['middle_name'] !== '', function ($query) use ($name) {
                    $query->orWhere('middle_name', 'like', '%' . $name['middle_name'] . '%');
                })
                ->when($name['last_name'] !== '', function ($query) use ($name) {
                    $query->orWhere('last_name', 'like', '%' . $name['last_name'] . '%');
                });
        } else {
            $query->where('first_name', 'like', '%' . $value . '%')
                ->orWhere('middle_name', 'like', '%' . $value . '%')
                ->orWhere('last_name', 'like', '%' . $value . '%');
        }
    }
}
