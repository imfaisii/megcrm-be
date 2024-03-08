<?php

namespace App\Http\Controllers;

use App\Actions\Common\BaseJsonResource;
use App\Actions\FuelTypes\DeleteFuelTypeAction;
use App\Actions\FuelTypes\ListFuelTypeAction;
use App\Actions\FuelTypes\StoreFuelTypeAction;
use App\Actions\FuelTypes\UpdateFuelTypeAction;
use App\Http\Requests\FuelTypes\StoreFuelTypeRequest;
use App\Models\FuelType;
use Illuminate\Http\Resources\Json\ResourceCollection;

use function App\Helpers\null_resource;

class FuelTypeController extends Controller
{
    public function index(ListFuelTypeAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();

        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(StoreFuelTypeRequest $request, StoreFuelTypeAction $action)
    {
        $action->create($request->validated());

        return null_resource();
    }

    public function update(FuelType $fuelType, StoreFuelTypeRequest $request, UpdateFuelTypeAction $action): BaseJsonResource
    {
        $action->enableQueryBuilder();

        return $action->individualResource($action->update($fuelType, $request->validated()));
    }

    public function destroy(FuelType $FuelType, DeleteFuelTypeAction $action): BaseJsonResource
    {
        $action->delete($FuelType);

        return null_resource();
    }
}
