<?php

namespace App\Http\Requests\Leads;

use App\Actions\Common\BaseFormRequest;

class GetAddressRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'post_code' => ['required', 'string']
        ];
    }
}
