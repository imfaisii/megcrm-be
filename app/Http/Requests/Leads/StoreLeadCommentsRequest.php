<?php

namespace App\Http\Requests\Leads;

use App\Actions\Common\BaseFormRequest;

class StoreLeadCommentsRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'comments' => ['required', 'string'],
        ];
    }
}
