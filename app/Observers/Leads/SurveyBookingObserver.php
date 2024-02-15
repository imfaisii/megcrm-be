<?php

namespace App\Observers\Leads;

use App\Models\SurveyBooking;

class SurveyBookingObserver
{
    public function updated(SurveyBooking $surveyBooking): void
    {
        if (in_array('surveyor_id', $surveyBooking->getDirty())) {
            
        }
    }
}
