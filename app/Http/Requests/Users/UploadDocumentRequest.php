<?php

namespace App\Http\Requests\Users;

use App\Actions\Common\BaseFormRequest;

class UploadDocumentRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', 'file']
        ];
    }
}
