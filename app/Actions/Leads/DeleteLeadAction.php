<?php

namespace App\Actions\Leads;

use App\Actions\Common\AbstractDeleteAction;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteLeadAction extends AbstractDeleteAction
{
    protected string $modelClass = User::class;
}
