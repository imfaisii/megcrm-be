<?php

namespace App\Actions\Common;

use App\Traits\Common\NewQueryTrait;

abstract class AbstractDeleteAction
{
    use NewQueryTrait;

    public function delete($model): ?bool
    {
        return $model->delete();
    }

    public function force(BaseModel $model): ?bool
    {
        return $model->forceDelete();
    }
}
