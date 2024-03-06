<?php

namespace App\Http\Requests\AirCall;

use App\Actions\Common\BaseFormRequest;
use App\Rules\E164NumberCheckRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class AirCallRequest extends BaseFormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone_number' => ['required', 'string', 'min:10', 'max:20', new E164NumberCheckRule],
            'specific_agent' => ['sometimes', 'required', 'boolean', 'exclude'],
        ];
    }

    public function passedValidation()
    {
        $this->merge([
            'user_id' => auth()?->user()?->air_caller_id,
        ]);
    }

    // leter if we want to show specific call from the logged in user then we can uncomment it

    public function validated($key = null, $default = null): array
    {

        $routeName = Str::between($this->route()->getName(), '.', '-');

        switch ($routeName) {
            case 'search':
                return $this->FinalRequestForCallSearch();
                break;
            case 'make':
                return $this->FinalRequestForCallMake();
                break;
            case 'dial':
                return $this->FinalRequestForCallDial();
                break;
            default:
                return $this->validator->validated();
                break;
        }

    }

    private function FinalRequestForCallSearch()
    {
        return filled($this->specific_agent) ? [...$this->validator->validated(), 'user_id' => auth()?->user()?->air_caller_id]
            : $this->validator->validated();
    }

    private function FinalRequestForCallMake()
    {
        $array = [...$this->validator->validated(),  'to' => $this->phone_number, 'user_id' => auth()?->user()?->air_caller_id, 'number_id' => Arr::random(auth()?->user()?->phone_number_aircall ?? config('credentials.AIRCALL_PHONE_NUMBER'))];

        return Arr::except($array, 'phone_number');
    }

    private function FinalRequestForCallDial()
    {
        $array = [...$this->validator->validated(), 'user_id' => auth()?->user()?->air_caller_id, 'to' => $this->phone_number];

        return Arr::except($array, 'phone_number');

    }
}
