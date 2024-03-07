<?php

namespace App\Http\Requests\Sms;

use App\Actions\Common\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class SendSmsRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:1000']
        ];
    }
}
