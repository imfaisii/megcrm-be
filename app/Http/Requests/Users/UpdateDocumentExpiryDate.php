<?php

namespace App\Http\Requests\Users;

use App\Actions\Common\BaseFormRequest;

class UpdateDocumentExpiryDate extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'expiry' => ['required', 'date'],
        ];
    }
}
