<?php

namespace App\Actions\LeadSources;

use App\Actions\Common\AbstractListAction;
use App\Models\LeadSource;

class ListLeadSourceAction extends AbstractListAction
{
    protected string $modelClass = LeadSource::class;
}
