<?php

namespace App\Observers;

use App\Models\Lead;

use function App\Helpers\generateUniqueRandomString;

class LeadObserver
{
    /**
     * Handle the Lead "created" event.
     */
    public function creating(Lead $lead): void
    {
        //
        $lead->reference_number = generateUniqueRandomString();
    }

    /**
     * Handle the Lead "updated" event.
     */
    public function updated(Lead $lead): void
    {
        //
    }

    /**
     * Handle the Lead "deleted" event.
     */
    public function deleted(Lead $lead): void
    {
        //
    }

    /**
     * Handle the Lead "restored" event.
     */
    public function restored(Lead $lead): void
    {
        //
    }

    /**
     * Handle the Lead "force deleted" event.
     */
    public function forceDeleted(Lead $lead): void
    {
        //
    }
}
