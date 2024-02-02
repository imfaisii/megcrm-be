<?php

namespace App\Http\Requests\Measures;

use App\Actions\Common\BaseFormRequest;

class StoreMeasureRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
