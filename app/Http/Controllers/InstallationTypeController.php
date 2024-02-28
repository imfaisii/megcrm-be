<?php

namespace App\Http\Controllers;

use App\Actions\Common\BaseJsonResource;
use App\Actions\InstallationTypes\DeleteInstallationTypeAction;
use App\Actions\InstallationTypes\ListInstallationTypeAction;
use App\Actions\InstallationTypes\StoreInstallationTypeAction;
use App\Actions\InstallationTypes\UpdateInstallationTypeAction;
use App\Http\Requests\InstallationTypes\StoreInstallationTypeRequest;
use App\Models\InstallationType;
use Illuminate\Http\Resources\Json\ResourceCollection;

use function App\Helpers\null_resource;

class InstallationTypeController extends Controller
{
    public function index(ListInstallationTypeAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();
        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(StoreInstallationTypeRequest $request, StoreInstallationTypeAction $action)
    {
        $action->create($request->validated());
        return null_resource();
    }

    public function update(InstallationType $installationType, StoreInstallationTypeRequest $request,  UpdateInstallationTypeAction $action): BaseJsonResource
    {
        $action->enableQueryBuilder();
        return $action->individualResource($action->update($installationType, $request->validated()));
    }

    public function destroy(InstallationType $installationType, DeleteInstallationTypeAction $action): BaseJsonResource
    {
        $action->delete($installationType);
        return null_resource();
    }
}
