<?php

namespace App\Http\Controllers;

use App\Actions\Common\BaseJsonResource;
use App\Actions\JobTypes\DeleteJobTypeAction;
use App\Actions\JobTypes\ListJobTypeAction;
use App\Actions\JobTypes\StoreJobTypeAction;
use App\Actions\JobTypes\UpdateJobTypeAction;
use App\Http\Requests\JobTypes\StoreJobTypeRequest;
use App\Models\JobType;
use Illuminate\Http\Resources\Json\ResourceCollection;

use function App\Helpers\null_resource;

class JobTypeController extends Controller
{
    public function index(ListJobTypeAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();

        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(StoreJobTypeRequest $request, StoreJobTypeAction $action)
    {
        $action->create($request->validated());

        return null_resource();
    }

    public function update(JobType $jobType, StoreJobTypeRequest $request, UpdateJobTypeAction $action): BaseJsonResource
    {
        $action->enableQueryBuilder();

        return $action->individualResource($action->update($jobType, $request->validated()));
    }

    public function destroy(JobType $JobType, DeleteJobTypeAction $action): BaseJsonResource
    {
        $action->delete($JobType);

        return null_resource();
    }
}
