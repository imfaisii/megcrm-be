<?php

namespace App\Actions\AirCall;

use App\Fascade\AirCallFascade;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class DialAirCallAction
{
    public function dial(array $data): JsonResponse
    {
        return AirCallFascade::dialCall(userId: Arr::get($data, 'user_id'), queryParams: Arr::except($data, ['user_id']));
    }
}
