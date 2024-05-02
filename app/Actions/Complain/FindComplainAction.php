<?php

namespace App\Actions\Comaplain;

use App\Actions\Common\AbstractFindAction;
use App\Actions\Common\BaseModel;
use App\Enums\Users\MediaCollectionEnum;
use App\Models\Complaints;

class FindComplainAction extends AbstractFindAction
{
    protected string $modelClass = Complaints::class;

    public function findOrFail($primaryKey, array $columns = ['*']): BaseModel
    {
        $lead = parent::findOrFail($primaryKey);
        return $lead;
    }
}
