<?php

namespace App\Http\Controllers;

use App\Actions\Calendars\ListCalendarAction;
use App\Actions\Calendars\StoreCalendarAction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CalendarController extends Controller
{
    public function index(ListCalendarAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();

        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(Request $request, StoreCalendarAction $action)
    {
        $lead = $action->create($request->validated());

        return $action->individualResource($lead);
    }
}
