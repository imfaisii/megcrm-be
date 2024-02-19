<?php

namespace App\Http\Requests\JobTypes;

use App\Actions\Common\BaseFormRequest;

class StoreJobTypeRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
