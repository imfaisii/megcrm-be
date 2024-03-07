<?php

namespace App\Actions\AirCall;

use App\Fascade\AirCallFascade;
use Illuminate\Http\JsonResponse;

class SearchAirCallAction
{
    public function search(array $data): JsonResponse
    {
        return AirCallFascade::searchCall($data);
    }
}
