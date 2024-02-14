<?php

namespace App\Http\Controllers;

use App\Actions\CalendereEvent\ListCalenderEventAction;
use App\Actions\CalendereEvent\StoreCalenderEventAction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CalenderEventsController extends Controller
{
    
    public function index(ListCalenderEventAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();
        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(Request $request, StoreCalenderEventAction $action)
    {
        $lead = $action->create($request->validated());
        return $action->individualResource($lead);
    }
}
