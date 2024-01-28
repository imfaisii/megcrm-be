<?php

namespace App\Http\Controllers\Leads;

use App\Actions\Leads\ListLeadAction;
use App\Http\Controllers\Controller;
use App\Models\BenefitType;
use App\Models\FuelType;
use App\Models\JobType;
use App\Models\LeadGenerator;
use App\Models\LeadSource;
use App\Models\Measure;
use App\Models\Surveyor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LeadController extends Controller
{
    public function index(ListLeadAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();
        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    public function getExtras() {
        $data = [
            'job_types' => JobType::all(),
            'fuel_types' => FuelType::all(),
            'surveyors' => Surveyor::all(),
            'measures' => Measure::all(),
            'benefit_types' => BenefitType::all(),
            'lead_generators' => LeadGenerator::all(),
            'lead_sources' => LeadSource::all(),
        ];

        return $this->success(data: $data);
    }
}
