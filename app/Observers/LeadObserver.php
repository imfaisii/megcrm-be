<?php

namespace App\Observers;

use App\Models\Lead;

use function App\Helpers\formatPostCodeWithSpace;
use function App\Helpers\generateUniqueRandomString;

class LeadObserver
{
    public function creating(Lead $lead): void
    {
        $lead->reference_number = generateUniqueRandomString();
        $lead->actual_post_code = formatPostCodeWithSpace($lead->post_code);
    }
}
