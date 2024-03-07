<?php

namespace App\Jobs\Emails;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VisaExpirationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $visaExpiryMembers;


    public function __construct()
    {
        $this->visaExpiryMembers = User::whereHas('additional', function ($query) {
            $query->where('visa_expiry');
        });
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
