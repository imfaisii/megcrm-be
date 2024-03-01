<?php

namespace App\Actions\AirCall;

use App\Fascade\AirCallFascade;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class MakeAirCallAction
{

    public function make(array $data): JsonResponse
    {
        return AirCallFascade::startACall(userId: Arr::get($data, 'user_id'), queryParams: Arr::except($data, ['user_id']));
    }
}
