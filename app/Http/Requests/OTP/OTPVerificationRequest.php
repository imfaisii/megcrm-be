<?php

namespace App\Http\Requests\OTP;

use Illuminate\Foundation\Http\FormRequest;

class OTPVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'OTP_Code' => ['required', 'string', 'size:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'OTP_Code.required' => 'OTP Code is required',
            'OTP_Code.size' => 'OTP Code must be 6 digits',
        ];
    }
}
