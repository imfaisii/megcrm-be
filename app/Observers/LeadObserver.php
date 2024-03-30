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
        $lead->plain_address = trim($lead->plain_address);
        $lead->post_code = trim($lead->post_code);
        $lead->address = trim($lead->address);
        $lead->building_number = trim($lead->building_number);
        $lead->sub_building = trim($lead->sub_building);
    }
}
