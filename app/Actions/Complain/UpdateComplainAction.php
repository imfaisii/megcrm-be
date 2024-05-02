<?php

namespace App\Actions\Complain;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\Complaints;
use Illuminate\Support\Arr;

class UpdateComplainAction extends AbstractUpdateAction
{
    protected string $modelClass = Complaints::class;

    public function update(mixed $lead, array $data): mixed
    {
        return 1;
    }


}
