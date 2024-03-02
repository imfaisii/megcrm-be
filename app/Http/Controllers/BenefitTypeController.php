<?php

namespace App\Http\Controllers;

use App\Actions\BenefitTypes\DeleteBenefitTypeAction;
use App\Actions\BenefitTypes\ListBenefitTypeAction;
use App\Actions\BenefitTypes\StoreBenefitTypeAction;
use App\Actions\BenefitTypes\UpdateBenefitTypeAction;
use App\Actions\Common\BaseJsonResource;
use App\Http\Requests\BenefitTypes\StoreBenefitTypeRequest;
use App\Models\BenefitType;
use Illuminate\Http\Resources\Json\ResourceCollection;

use function App\Helpers\null_resource;

class BenefitTypeController extends Controller
{
    public function index(ListBenefitTypeAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();

        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(StoreBenefitTypeRequest $request, StoreBenefitTypeAction $action)
    {
        $action->create($request->validated());

        return null_resource();
    }

    public function update(BenefitType $benefitType, StoreBenefitTypeRequest $request, UpdateBenefitTypeAction $action): BaseJsonResource
    {
        $action->enableQueryBuilder();

        return $action->individualResource($action->update($benefitType, $request->validated()));
    }

    public function destroy(BenefitType $BenefitType, DeleteBenefitTypeAction $action): BaseJsonResource
    {
        $action->delete($BenefitType);

        return null_resource();
    }
}
