<?php

namespace App\Http\Controllers\AirCall;

use App\Actions\AirCall\DialAirCallAction;
use App\Actions\AirCall\MakeAirCallAction;
use App\Actions\AirCall\SearchAirCallAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\AirCall\AirCallRequest;
use Illuminate\Http\Request;

class AirCallController extends Controller
{

    public function searchCall(AirCallRequest $request, SearchAirCallAction $action)
    {
        return $action->search($request->validated());
    }

    public function dialCall(AirCallRequest $request, DialAirCallAction $action)
    {

        return $action->dial($request->validated());
    }

    public function makeCall(AirCallRequest $request, MakeAirCallAction $action)
    {
        dd($request->validated());
        return $action->make($request->validated());
    }
}
