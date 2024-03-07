<?php

namespace App\Traits\Common;

use App\Actions\Common\BaseJsonResource;
use App\Actions\Common\BaseResourceCollection;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection as IlluminateSupportCollection;

/**
 * @property string $modelClass
 */
trait HasApiResource
{
    /**
     * @param  Collection|Paginator  $collection
     */
    public function resourceCollection(Paginator|Collection|IlluminateSupportCollection $collection): ResourceCollection
    {
        return new BaseResourceCollection($collection, $this->modelClass);
    }

    /**
     * @param  \App\Actions\Common\BaseModel  $model
     */
    public function individualResource($model): JsonResource
    {
        return new BaseJsonResource($model);
    }
}
