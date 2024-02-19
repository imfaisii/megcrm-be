<?php

namespace App\Actions\LeadSources;

use App\Actions\Common\AbstractDeleteAction;
use App\Models\LeadSource;

class DeleteLeadSourceAction extends AbstractDeleteAction
{
    protected string $modelClass = LeadSource::class;
}
