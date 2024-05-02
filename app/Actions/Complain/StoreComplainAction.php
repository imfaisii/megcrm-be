<?php

namespace App\Actions\Complain;

use App\Actions\Common\AbstractCreateAction;
use App\Jobs\AircallContactCreationJob;
use App\Models\Complaints;
use Illuminate\Support\Arr;

class StoreComplainAction extends AbstractCreateAction
{
    protected string $modelClass = Complaints::class;

    public function create(array $data): Complaints
    {
        return Complaints::create($data);
    }
}
