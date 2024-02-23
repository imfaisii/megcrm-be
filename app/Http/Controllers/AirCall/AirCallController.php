<?php

namespace App\Http\Controllers\AirCall;

use App\Actions\AirCall\SearchAirCallAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\AirCall\AirCallSearchRequest;
use Illuminate\Http\Request;

class AirCallController extends Controller
{

    public function searchCall(AirCallSearchRequest $request, SearchAirCallAction $action)
    {
        return  $action->search($request->validated());
    }
}
