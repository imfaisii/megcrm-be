<?php

namespace App\Http\Controllers;

use App\Actions\CallCenters\CallCenterStatuses\DeleteCallCenterStatusAction;
use App\Actions\CallCenters\CallCenterStatuses\ListCallCenterStatusAction;
use App\Actions\CallCenters\CallCenterStatuses\StoreCallCenterStatusAction;
use App\Actions\CallCenters\CallCenterStatuses\UpdateCallCenterStatusAction;
use App\Actions\Common\BaseJsonResource;
use App\Http\Requests\CallCenter\Statuses\StoreCallCenterStatusRequest;
use App\Http\Requests\CallCenter\Statuses\UpdateCallCenterStatusRequest;
use App\Models\CallCenterStatus;
use Illuminate\Http\Resources\Json\ResourceCollection;

use function App\Helpers\null_resource;

class CallCenterStatusesController extends Controller
{
    public function index(ListCallCenterStatusAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();
        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(StoreCallCenterStatusRequest $request, StoreCallCenterStatusAction $action)
    {
        $action->create($request->validated());
        return null_resource();
    }

    public function update(CallCenterStatus $callCenterStatus, UpdateCallCenterStatusRequest $request,  UpdateCallCenterStatusAction $action): BaseJsonResource
    {
        $action->enableQueryBuilder();
        return $action->individualResource($action->update($callCenterStatus, $request->validated()));
    }

    public function destroy(CallCenterStatus $callCenterStatus, DeleteCallCenterStatusAction $action): BaseJsonResource
    {
        $action->delete($callCenterStatus);
        return null_resource();
    }
}
