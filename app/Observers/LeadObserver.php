<?php

namespace App\Observers;

use App\Jobs\GetEpcScrappedDataOfLead;
use App\Models\Lead;

use function App\Helpers\generateUniqueRandomString;

class LeadObserver
{
    public function creating(Lead $lead): void
    {
        $lead->reference_number = generateUniqueRandomString();
        $lead->plain_address = $lead->plain_address ? trim($lead->plain_address) : null;
        $lead->post_code = $lead->post_code ? trim($lead->post_code) : null;
        $lead->address = $lead->address ? trim($lead->address) : null;
        $lead->building_number = $lead->building_number ? trim($lead->building_number) : null;
        $lead->sub_building = $lead->sub_building ? trim($lead->sub_building) : null;
    }

    public function created(Lead $lead): void
    {
        dispatch(new GetEpcScrappedDataOfLead($lead));
    }
}
