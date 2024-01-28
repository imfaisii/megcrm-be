<?php

namespace App\Actions\Leads;

use App\Actions\Common\AbstractFindAction;
use App\Models\User;

class FindLeadAction extends AbstractFindAction
{
    protected string $modelClass = User::class;
}
