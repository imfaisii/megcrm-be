<?php

namespace App\Http\Controllers;

use App\Actions\CallCenters\DeleteCallCenterAction;
use App\Actions\CallCenters\ListCallCenterAction;
use App\Actions\CallCenters\StoreCallCenterAction;
use App\Actions\CallCenters\UpdateCallCenterAction;
use App\Actions\Common\BaseJsonResource;
use App\Http\Requests\CallCenter\StoreCallCenterRequest;
use App\Models\CallCenter;
use Illuminate\Http\Resources\Json\ResourceCollection;

use function App\Helpers\null_resource;

class CallCenterController extends Controller
{
    public function index(ListCallCenterAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();
        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(StoreCallCenterRequest $request, StoreCallCenterAction $action)
    {
        $action->create($request->validated());
        return null_resource();
    }

    public function update(CallCenter $callCenter, StoreCallCenterRequest $request,  UpdateCallCenterAction $action): BaseJsonResource
    {
        $action->enableQueryBuilder();
        return $action->individualResource($action->update($callCenter, $request->validated()));
    }

    public function destroy(CallCenter $callCenter, DeleteCallCenterAction $action): BaseJsonResource
    {
        $action->delete($callCenter);
        return null_resource();
    }
}
