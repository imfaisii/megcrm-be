<?php

namespace App\Observers\Leads;

use App\Models\Lead;
use function App\Helpers\formatCommas;
use function App\Helpers\removeSpace;

class LeadObserver
{
    public function saving(Lead $lead): void
    {
        $lead->address = strtolower(formatCommas($lead->address));
        $lead->post_code = strtolower(removeSpace(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $lead->post_code)));
    }
}
