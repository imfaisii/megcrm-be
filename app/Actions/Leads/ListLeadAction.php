<?php

namespace App\Actions\Leads;

use App\Actions\Common\AbstractListAction;
use App\Models\Lead;

class ListLeadAction extends AbstractListAction
{
    protected string $modelClass = Lead::class;
}
