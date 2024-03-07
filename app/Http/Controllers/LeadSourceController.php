<?php

namespace App\Http\Controllers;

use App\Actions\Common\BaseJsonResource;
use App\Actions\LeadSources\DeleteLeadSourceAction;
use App\Actions\LeadSources\ListLeadSourceAction;
use App\Actions\LeadSources\StoreLeadSourceAction;
use App\Actions\LeadSources\UpdateLeadSourceAction;
use App\Http\Requests\LeadSources\StoreLeadSourceRequest;
use App\Models\LeadSource;
use Illuminate\Http\Resources\Json\ResourceCollection;

use function App\Helpers\null_resource;

class LeadSourceController extends Controller
{
    public function index(ListLeadSourceAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();

        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(StoreLeadSourceRequest $request, StoreLeadSourceAction $action)
    {
        $action->create($request->validated());

        return null_resource();
    }

    public function update(LeadSource $leadSource, StoreLeadSourceRequest $request, UpdateLeadSourceAction $action): BaseJsonResource
    {
        $action->enableQueryBuilder();

        return $action->individualResource($action->update($leadSource, $request->validated()));
    }

    public function destroy(LeadSource $LeadSource, DeleteLeadSourceAction $action): BaseJsonResource
    {
        $action->delete($LeadSource);

        return null_resource();
    }
}
