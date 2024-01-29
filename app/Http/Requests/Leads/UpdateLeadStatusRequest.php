<?php

namespace App\Http\Requests\Leads;

use App\Enums\Leads\StatusEnum;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', new EnumValue(StatusEnum::class)],
            'comments' => ['required', 'max:255']
        ];
    }
}
