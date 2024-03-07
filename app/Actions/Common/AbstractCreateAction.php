<?php

namespace App\Actions\Common;

use App\Traits\Common\HasApiResource;
use App\Traits\Common\NewQueryTrait;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

abstract class AbstractCreateAction
{
    use HasApiResource;
    use NewQueryTrait;

    /**
     * @return BaseModel|Collection
     */
    public function create(array $data): BaseModel|Collection|Role|Permission
    {
        return $this->newQuery()->create($data);
    }
}
