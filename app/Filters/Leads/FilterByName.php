<?php

namespace App\Filters\Leads;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;

use function App\Helpers\split_name;

class FilterByName implements Filter
{
    /**
     * @phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter
     *
     * @phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
     */
    public function __invoke(Builder $query, $value, string $property): void
    {
        if (Str::contains($value, ' ')) {
            $name = split_name($value);

            $query->where(function ($query) use ($name) {
                $query->where('first_name', 'like', '%' . $name['first_name'] . '%')
                    ->when($name['middle_name'] !== '', function ($query) use ($name) {
                        $query->orWhere('middle_name', 'like', '%' . $name['middle_name'] . '%');
                    })
                    ->when($name['last_name'] !== '', function ($query) use ($name) {
                        $query->orWhere('last_name', 'like', '%' . $name['last_name'] . '%');
                    });
            });
        } else {
            $query->where(function ($query) use ($value) {
                $query->where('first_name', 'like', '%' . $value . '%')
                    ->orWhere('middle_name', 'like', '%' . $value . '%')
                    ->orWhere('last_name', 'like', '%' . $value . '%');
            });
        }
    }
}
