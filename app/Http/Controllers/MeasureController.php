<?php

namespace App\Http\Controllers;

use App\Actions\Common\BaseJsonResource;
use App\Actions\Measures\DeleteMeasureAction;
use App\Actions\Measures\ListMeasureAction;
use App\Actions\Measures\StoreMeasureAction;
use App\Actions\Measures\UpdateMeasureAction;
use App\Http\Requests\Measures\StoreMeasureRequest;
use App\Models\Measure;
use Illuminate\Http\Resources\Json\ResourceCollection;

use function App\Helpers\null_resource;

class MeasureController extends Controller
{
    public function index(ListMeasureAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();
        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(StoreMeasureRequest $request, StoreMeasureAction $action)
    {
        $action->create($request->validated());
        return null_resource();
    }

    public function update(Measure $measure, StoreMeasureRequest $request,  UpdateMeasureAction $action): BaseJsonResource
    {
        $action->enableQueryBuilder();
        return $action->individualResource($action->update($measure, $request->validated()));
    }

    public function destroy(Measure $Measure, DeleteMeasureAction $action): BaseJsonResource
    {
        $action->delete($Measure);
        return null_resource();
    }
}
