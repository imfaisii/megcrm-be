<?php

namespace App\Http\Requests\Users;

use App\Actions\Common\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', 'file']
        ];
    }
}
