<?php

namespace App\Sorts\Surveyors;

use Spatie\QueryBuilder\Sorts\Sort;

class UserRelationSort implements Sort
{
    public function __invoke($query, bool $descending, string $property)
    {
        $query->leftJoin('users', 'users.id', '=', 'surveyors.user_id')
            ->select('users.*')
            ->orderBy(str_replace("user", "users", $property), $descending ? 'desc' : 'asc');
    }
}
