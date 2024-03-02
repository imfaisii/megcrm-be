<?php

namespace App\Http\Requests\Leads;

use App\Actions\Common\BaseFormRequest;

class UploadLeadFileRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:xlsx,csv,xls'],
        ];
    }
}
