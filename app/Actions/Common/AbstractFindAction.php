<?php

namespace App\Actions\Common;

use App\Traits\Common\HasApiResource;
use App\Traits\Common\NewQueryTrait;

abstract class AbstractFindAction
{
    use HasApiResource, NewQueryTrait;

    /**
     * @param  array|string[]  $columns
     */
    public function find($primaryKey, array $columns = ['*']): ?BaseModel
    {
        return $this->getQuery()->find($primaryKey, $columns);
    }

    /**
     * @param  array|string[]  $columns
     */
    public function findOrFail($primaryKey, array $columns = ['*']): BaseModel
    {
        return $this->getQuery()->findOrFail($primaryKey, $columns);
    }

    /**
     * @param  array|string[]  $columns
     */
    public function findOrNew($primaryKey, array $columns = ['*']): BaseModel
    {
        return $this->getQuery()->findOrNew($primaryKey, $columns);
    }

    public function findByModel(BaseModel $model): BaseModel
    {
        return $model->applyQueryBuilder();
    }
}
