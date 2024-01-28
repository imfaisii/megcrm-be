<?php

namespace App\Actions\Leads;

use App\Actions\Common\AbstractDeleteAction;
use App\Models\Lead;

class DeleteLeadAction extends AbstractDeleteAction
{
    protected string $modelClass = Lead::class;
}
