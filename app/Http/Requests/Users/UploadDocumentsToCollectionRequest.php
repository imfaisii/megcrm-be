<?php

namespace App\Http\Requests\Users;

use App\Actions\Common\BaseFormRequest;

class UploadDocumentsToCollectionRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', 'file'],
            'collection' => ['required', 'string'],
            'expiry' => ['nullable', 'date']
        ];
    }
}
