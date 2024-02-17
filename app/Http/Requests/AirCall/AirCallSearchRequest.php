<?php

namespace App\Http\Requests\AirCall;

use App\Actions\Common\BaseFormRequest;
use App\Rules\E164NumberCheckRule;
use Illuminate\Foundation\Http\FormRequest;

class AirCallSearchRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone_number' => ['required', 'string', 'max:20', new E164NumberCheckRule],
        ];
    }


    public function passedValidation()
    {
        $this->merge([
            'user_id' => auth()?->user()?->air_caller_id
        ]);
    }

    public function validated($key = null, $default = null): array
    {
        return [...$this->validator->validated(), 'user_id' => auth()?->user()?->air_caller_id];
    }
}
