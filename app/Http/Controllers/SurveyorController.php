<?php

namespace App\Http\Controllers;

use App\Actions\Common\BaseJsonResource;
use App\Actions\Surveyors\DeleteSurveyorAction;
use App\Actions\Surveyors\ListSurveyorAction;
use App\Actions\Surveyors\StoreSurveyorAction;
use App\Actions\Surveyors\UpdateSurveyorAction;
use App\Http\Requests\Surveyors\StoreSurveyorRequest;
use App\Models\Surveyor;
use Illuminate\Http\Request;
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

    public function update(Surveyor $Surveyor, StoreSurveyorRequest $request,  UpdateSurveyorAction $action): BaseJsonResource
    {
        $action->enableQueryBuilder();
        return $action->individualResource($action->update($Surveyor, $request->validated()));
    }

    public function destroy(Surveyor $Surveyor, DeleteSurveyorAction $action): BaseJsonResource
    {
        $action->delete($Surveyor);
        return null_resource();
    }
}
