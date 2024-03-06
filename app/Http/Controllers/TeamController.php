<?php

namespace App\Http\Controllers;

use App\Actions\Common\BaseJsonResource;
use App\Actions\Team\ListTeamAction;
use App\Actions\Team\StoreTeamAction;
use App\Actions\Team\UpdateTeamAction;
use App\Http\Requests\Team\ListTeamRequest;
use App\Http\Requests\team\StoreTeamRequest;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

use function App\Helpers\null_resource;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ListTeamAction $action, ListTeamRequest $listTeamRequest): ResourceCollection
    {
        $action->enableQueryBuilder();
        return $action->resourceCollection($action->listOrPaginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeamRequest $request, StoreTeamAction $action): JsonResource
    {
        $team = $action->create($request->validated());

        return $action->individualResource($team);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Team $team, StoreTeamRequest $request, UpdateTeamAction $action): BaseJsonResource
    {
        $action->enableQueryBuilder();

        return $action->individualResource($action->update($team, $request->validated()));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $user, DeleteUserAction $action): BaseJsonResource
    {
        $action->delete($user);

        return null_resource();
    }
}
