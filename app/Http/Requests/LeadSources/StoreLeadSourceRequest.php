<?php

namespace App\Http\Requests\LeadSources;

use App\Actions\Common\BaseFormRequest;

class StoreLeadSourceRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
