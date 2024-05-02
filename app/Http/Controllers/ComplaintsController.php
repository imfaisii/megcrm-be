<?php

namespace App\Http\Controllers;

use App\Actions\Complain\ListComplainAction;
use App\Actions\Complain\UpdateComplainAction;
use App\Http\Requests\Complain\StoreComplainRequest;
use App\Http\Requests\Complain\UpdateComplainRequest;
use App\Models\Complaints;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

use function App\Helpers\null_resource;

class ComplaintsController extends Controller
{
    public function index(ListComplainAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();

        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(StoreComplainRequest $request, StoreComplainRequest $action)
    {
        $lead = $action->create($request->validated());

        return $action->individualResource($lead);
    }

    public function show(Complaints $lead, FindLeadAction $action)
    {
        $action->enableQueryBuilder();

        return $action->individualResource($action->findOrFail($lead->id));
    }

    public function update(Complaints $lead, UpdateComplainRequest $request, UpdateComplainAction $action)
    {
        $action->enableQueryBuilder();

        return $action->individualResource($action->update($lead, $request->validated()));
    }

    public function destroy(Lead $lead, DeleteLeadAction $action)
    {
        $action->delete($lead);

        return null_resource();
    }

}
