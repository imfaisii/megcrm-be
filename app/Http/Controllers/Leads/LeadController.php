<?php

namespace App\Http\Controllers\Leads;

use App\Actions\Leads\DeleteLeadAction;
use App\Actions\Leads\ListLeadAction;
use App\Actions\Leads\StoreLeadAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Leads\StoreLeadRequest;
use App\Http\Requests\Leads\UpdateLeadStatusRequest;
use App\Models\BenefitType;
use App\Models\FuelType;
use App\Models\JobType;
use App\Models\Lead;
use App\Models\LeadGenerator;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\Measure;
use App\Models\Surveyor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

use function App\Helpers\null_resource;

class LeadController extends Controller
{
    public function index(ListLeadAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();
        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(StoreLeadRequest $request, StoreLeadAction $action)
    {
        $action->create($request->validated());
        return null_resource();
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(Lead $lead, DeleteLeadAction $action)
    {
        $action->delete($lead);
        return null_resource();
    }

    public function getExtras()
    {
        $data = [
            'job_types' => JobType::all(),
            'fuel_types' => FuelType::all(),
            'surveyors' => Surveyor::all(),
            'measures' => Measure::all(),
            'benefit_types' => BenefitType::all(),
            'lead_generators' => LeadGenerator::all(),
            'lead_sources' => LeadSource::all(),
            'lead_statuses' => LeadStatus::all(),
            'lead_table_filters' => LeadStatus::take(6)->get()
        ];

        return $this->success(data: $data);
    }

    public function updateStatus(Lead $lead, UpdateLeadStatusRequest $request)
    {
        $lead->setStatus($request->status, $request->comments);
        return null_resource();
    }
}
