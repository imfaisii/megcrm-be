<?php

namespace App\Http\Controllers;

use App\Actions\Common\BaseJsonResource;
use App\Actions\LeadGenerators\DeleteLeadGeneratorAction;
use App\Actions\LeadGenerators\ListLeadGeneratorAction;
use App\Actions\LeadGenerators\StoreLeadGeneratorAction;
use App\Actions\LeadGenerators\UpdateLeadGeneratorAction;
use App\Http\Requests\LeadGenerators\StoreLeadGeneratorRequest;
use App\Models\LeadGenerator;
use Illuminate\Http\Resources\Json\ResourceCollection;

use function App\Helpers\null_resource;

class LeadGeneratorController extends Controller
{
    public function index(ListLeadGeneratorAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();
        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(StoreLeadGeneratorRequest $request, StoreLeadGeneratorAction $action)
    {
        $action->create($request->validated());
        return null_resource();
    }

    public function update(LeadGenerator $leadGenerator, StoreLeadGeneratorRequest $request,  UpdateLeadGeneratorAction $action): BaseJsonResource
    {
        $action->enableQueryBuilder();
        return $action->individualResource($action->update($leadGenerator, $request->validated()));
    }

    public function destroy(LeadGenerator $LeadGenerator, DeleteLeadGeneratorAction $action): BaseJsonResource
    {
        $action->delete($LeadGenerator);
        return null_resource();
    }
}
