<?php

namespace App\Actions\LeadSources;

use App\Actions\Common\AbstractCreateAction;
use App\Models\LeadSource;

class StoreLeadSourceAction extends AbstractCreateAction
{
    protected string $modelClass = LeadSource::class;
}
