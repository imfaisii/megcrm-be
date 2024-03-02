<?php

namespace App\Actions\Common;

use App\Traits\Common\HasApiResource;
use App\Traits\Common\NewQueryTrait;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AbstractUpdateAction
{
    use HasApiResource;
    use NewQueryTrait;

    /**
     * @param  BaseModel|Role|Permission  $model
     * @return BaseModel|Role|Permission
     */
    public function update(mixed $model, array $data): mixed
    {
        return tap($model, function (BaseModel|Role|Permission $model) use ($data) {
            $model->update($data);
        });
    }
}
