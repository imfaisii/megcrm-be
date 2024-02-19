<?php

namespace App\Actions\LeadSources;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\LeadSource;

class UpdateLeadSourceAction extends AbstractUpdateAction
{
    protected string $modelClass = LeadSource::class;
}
