<?php

namespace App\Http\Controllers\Leads;

use App\Actions\Common\BaseJsonResource;
use App\Actions\Leads\Statuses\DeleteLeadStatusAction;
use App\Actions\Leads\Statuses\ListLeadStatusAction;
use App\Actions\Leads\Statuses\StoreLeadStatusAction;
use App\Actions\Leads\Statuses\UpdateLeadStatusAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Leads\Statuses\StoreLeadStatusRequest;
use App\Models\LeadStatus;
use Illuminate\Http\Resources\Json\ResourceCollection;

use function App\Helpers\null_resource;

class StatusController extends Controller
{
    public function index(ListLeadStatusAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();

        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(StoreLeadStatusRequest $request, StoreLeadStatusAction $action)
    {
        $action->create($request->validated());

        return null_resource();
    }

    public function update(LeadStatus $leadStatus, StoreLeadStatusRequest $request, UpdateLeadStatusAction $action): BaseJsonResource
    {
        $action->enableQueryBuilder();

        return $action->individualResource($action->update($leadStatus, $request->validated()));
    }

    public function destroy(LeadStatus $leadStatus, DeleteLeadStatusAction $action): BaseJsonResource
    {
        $action->delete($leadStatus);

        return null_resource();
    }
}
