<?php

namespace App\Sorts\Surveyors;

use Spatie\QueryBuilder\Sorts\Sort;

class UserRelationSort implements Sort
{
    public function __invoke($query, bool $descending, string $property)
    {
        //
    }
}
