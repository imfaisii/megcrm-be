<?php

namespace App\Http\Controllers;

use App\Actions\CalenderEvents\ListCalenderEventAction;
use App\Actions\CalenderEvents\StoreCalenderEventAction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CalenderEventsController extends Controller
{
    public function index(ListCalenderEventAction $action): ResourceCollection
    {
        $action
            ->setUser(auth()->user())
            ->enableQueryBuilder();
        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(Request $request, StoreCalenderEventAction $action)
    {
        $lead = $action->create($request->validated());
        return $action->individualResource($lead);
    }
}
