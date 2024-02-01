<?php

namespace App\Http\Requests\Leads\Statuses;

use App\Actions\Common\BaseFormRequest;

class StoreLeadStatusRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'max:255']
        ];
    }
}
