<?php

namespace App\Actions\Leads;

use App\Enums\Permissions\RoleEnum;
use App\Models\BenefitType;
use App\Models\CallCenterStatus;
use App\Models\FuelType;
use App\Models\JobType;
use App\Models\LeadGenerator;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\Measure;
use App\Models\User;

class GetLeadExtrasAction
{
    public function __construct(protected ?User $user = null)
    {
        $this->user = $user ?? auth()->user();
    }

    public function execute(): array
    {
        $tableStatuses = [
            'Raw Lead',
            'Ready for Survey',
            'Waiting for Datamatch',
            'Ready for Installation',
            'Installed',
            'Follow Up',
            'Survey Booked',
            'Waiting for Boiler Picture',
            'Not interested',
            'Called from ring central',
            'Called from second number',
            'No answer',
        ];

        $both = [
            'Survey Booked',
            'Cancelled Survey',
            'Cancelled Job',
            'Cancelled Lead'
        ];


        if ($this->user->hasRole(RoleEnum::SURVEYOR)) {
            $leadGenerators = LeadGenerator::whereIn(
                'id',
                $this->user->leadGeneratorAssignments()->pluck('lead_generator_id')
            )->get();
        } else {
            $leadGenerators = LeadGenerator::all();
        }

        return [
            'job_types' => JobType::all(),
            'fuel_types' => FuelType::all(),
            'surveyors' => User::whereHas('roles', function ($query) {
                $query->where('name', RoleEnum::SURVEYOR);
            })->get(),
            'measures' => Measure::all(),
            'benefit_types' => BenefitType::all(),
            'lead_generators' => $leadGenerators,
            'lead_sources' => LeadSource::all(),
            'lead_statuses' => LeadStatus::all(),
            'lead_table_filters' => LeadStatus::whereIn('name', [...$tableStatuses, ...$both])->get(),
            'lead_jobs_filters' => LeadStatus::whereNotIn('name', $tableStatuses)->orWhere('name', $both)->get(),
            'call_center_statuses' => CallCenterStatus::all()
        ];
    }
}
