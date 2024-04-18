<?php

namespace App\Http\Controllers\Customer;

use App\Actions\Customer\ListCustomerLeadStatusAction;
use App\Actions\CustomerSupport\CustomerSupportEmailAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerSupport\CustomerSupportEmailRequest;
use Illuminate\Http\Resources\Json\JsonResource;

use function App\Helpers\meg_decrypts;

class CustomerController extends Controller
{
    public function lead_view(string $leadId, ListCustomerLeadStatusAction $action): JsonResource
    {
        $action->enableQueryBuilder();

        return $action->individualResource($action->findOrFail(meg_decrypts($leadId)));
    }

    public function supportEmail(string $leadId, CustomerSupportEmailAction $action, CustomerSupportEmailRequest $request)
    {
        $decrypted_id = meg_decrypts($leadId);
        return $action->sendEmail([...$request->validated(), 'id' => $decrypted_id]);

    }
}
