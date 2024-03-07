<?php

namespace App\Http\Controllers;

use App\Actions\Surveyors\ListSurveyorAction;
use App\Actions\Surveyors\StoreSurveyorAction;
use App\Http\Requests\Surveyors\StoreSurveyorRequest;
use Illuminate\Http\Resources\Json\ResourceCollection;

use function App\Helpers\null_resource;

class SurveyorController extends Controller
{
    public function index(ListSurveyorAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();

        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(StoreSurveyorRequest $request, StoreSurveyorAction $action)
    {
        $action->create($request->validated());

        return null_resource();
    }
}
