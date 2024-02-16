<?php

namespace App\Http\Controllers\Leads;

use App\Actions\Leads\DeleteLeadAction;
use App\Actions\Leads\FindLeadAction;
use App\Actions\Leads\ListLeadAction;
use App\Actions\Leads\StoreLeadAction;
use App\Actions\Leads\UpdateLeadAction;
use App\Enums\Permissions\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Leads\StoreLeadRequest;
use App\Http\Requests\Leads\UpdateLeadRequest;
use App\Http\Requests\Leads\UpdateLeadStatusRequest;
use App\Imports\Leads\LeadsImport;
use App\Models\BenefitType;
use App\Models\CallCenterStatus;
use App\Models\FuelType;
use App\Models\JobType;
use App\Models\Lead;
use App\Models\LeadGenerator;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\Measure;
use App\Models\Surveyor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;
use Imfaisii\ModelStatus\Status;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

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
        $lead = $action->create($request->validated());
        return $action->individualResource($lead);
    }

    public function show(int $lead, FindLeadAction $action)
    {
        $action->enableQueryBuilder();
        return $action->individualResource($action->findOrFail($lead));
    }

    public function update(Lead $lead, UpdateLeadRequest $request, UpdateLeadAction $action)
    {
        $action->enableQueryBuilder();
        return $action->individualResource($action->update($lead, $request->validated()));
    }

    public function destroy(Lead $lead, DeleteLeadAction $action)
    {
        $action->delete($lead);
        return null_resource();
    }

    public function getExtras()
    {
        $tableStatuses = [
            'Raw Lead',
            'Ready for Survey',
            'Waiting for Datamatch',
            'Ready for Installation',
            'Installed',
            'Follow Up',
            'Survey Booked',
            'Cancelled',
            'Waiting for Boiler Picture',
            'Not interested',
            'Called from ring central',
            'Called from second number',
            'No answer'
        ];

        $user = auth()->user();
        $leadGenerators = $user->hasRole(RoleEnum::SURVEYOR)
            ? LeadGenerator::whereIn('id', $user->leadGeneratorAssignments()
                ->pluck('lead_generator_id'))->get()
            : LeadGenerator::all();

        $data = [
            'job_types' => JobType::all(),
            'fuel_types' => FuelType::all(),
            'surveyors' => Surveyor::all(),
            'measures' => Measure::all(),
            'benefit_types' => BenefitType::all(),
            'lead_generators' => $leadGenerators,
            'lead_sources' => LeadSource::all(),
            'lead_statuses' => LeadStatus::all(),
            'lead_table_filters' => LeadStatus::whereIn('name', $tableStatuses)->get(),
            'lead_jobs_filters' => LeadStatus::whereNotIn('name', $tableStatuses)->get(),
            'call_center_statuses' => CallCenterStatus::all()
        ];

        return $this->success(data: $data);
    }

    public function updateStatus(Lead $lead, UpdateLeadStatusRequest $request)
    {
        if (str_contains(str()->lower($request->status), "survey booked")) {
            $lead->update([
                'is_marked_as_job' => true
            ]);
        }

        $lead->setStatus($request->status, $request->comments);
        return null_resource();
    }

    public function handleFileUpload(Request $request)
    {
        try {
            $matched = true;
            $exampleHeader = [
                "website",
                "name",
                "email",
                "contact_number",
                "dob",
                "postcode",
                "address",
                "what_is_your_home_ownership_status",
                "benefits",
            ];

            $headings = (new HeadingRowImport())->toArray($request->file('file'))[0][0];

            if (count($headings) < 8) {
                throw new Exception('File has invalid header. (less headings)' . json_encode($headings));
            }

            for ($i = 0; $i < 9; $i++) {
                if ($headings[$i] !== $exampleHeader[$i]) {
                    $matched = false;
                }
            }

            if (!$matched) {
                throw new Exception('File has invalid header ( not matched ).' . json_encode($headings));
            }

            Excel::import(new LeadsImport, $request->file('file'));

            return $this->success('File was uploaded successfully.');
        } catch (Exception $e) {
            Log::channel('lead_file_read_log')->info(
                "Error importing exception " . $e->getMessage()
            );
            return $this->error($e->getMessage());
        }
    }
}
