<?php

namespace App\Actions\Leads;

use App\Models\User;
use Http;

class GetOtherSitesLinkAction
{
    public function __construct(protected ?User $user = null)
    {
        $this->user = $user ?? auth()->user();
    }

    public function councilTax(string $postCode)
    {
        // Http::get()
    }
}
