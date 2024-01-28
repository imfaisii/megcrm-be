<?php

namespace App\Actions\Common;

use App\Traits\Common\NewQueryTrait;

abstract class AbstractDeleteAction
{
    use NewQueryTrait;

    /**
     * @return bool|null
     */
    public function delete($model): ?bool
    {
        return $model->delete();
    }

    /**
     * @param  BaseModel  $model
     * @return bool|null
     */
    public function force(BaseModel $model): ?bool
    {
        return $model->forceDelete();
    }
}
