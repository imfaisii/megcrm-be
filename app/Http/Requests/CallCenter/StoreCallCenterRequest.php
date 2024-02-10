<?php

namespace App\Http\Requests\CallCenter;

use App\Actions\Common\BaseFormRequest;

class StoreCallCenterRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'call_center_status_id' => ['required', 'exists:call_center_statuses,id'],
            'called_at' => ['required', 'date', 'before:now'],
            'comments' => ['required'],
            'lead_id' => ['required', 'exists:leads,id']
        ];
    }
}
