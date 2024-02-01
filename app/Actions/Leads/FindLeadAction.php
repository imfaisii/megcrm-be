<?php

namespace App\Actions\Leads;

use App\Actions\Common\AbstractFindAction;
use App\Models\Lead;

class FindLeadAction extends AbstractFindAction
{
    protected string $modelClass = Lead::class;
}
