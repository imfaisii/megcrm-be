<?php

namespace App\Http\Requests\LeadGenerators;

use App\Actions\Common\BaseFormRequest;

class StoreLeadGeneratorRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
