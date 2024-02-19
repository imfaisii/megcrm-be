<?php

namespace App\Http\Requests\CallCenter;

use App\Actions\Common\BaseFormRequest;

class StoreCallCenterRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'call_center_status_id' => ['required', 'exists:call_center_statuses,id'],
            'comments' => ['string', 'nullable'],
            'is_call_scheduled' => ['required', 'boolean'],
            'call_scheduled_time' => ['required_if:is_call_scheduled,true'],
            'lead_id' => ['required', 'exists:leads,id']
        ];
    }
}
