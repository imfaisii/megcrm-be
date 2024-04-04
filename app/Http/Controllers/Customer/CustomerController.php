<?php

namespace App\Http\Controllers\Customer;

use App\Actions\Customer\ListCustomerLeadStatusAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;

use function App\Helpers\meg_decrypts;

class CustomerController extends Controller
{

    public function lead_view(string $leadId, ListCustomerLeadStatusAction $action): JsonResource
    {
        $action->enableQueryBuilder();

        return $action->individualResource($action->findOrFail(meg_decrypts($leadId)));
    }
}
