<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Filters\Leads\FilterByName;
use App\Filters\Leads\FilterByPostcode;
use App\Filters\Leads\FilterByStatus;
use App\Models\SurveyBooking;
use App\Traits\Common\HasCalenderEvent;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Imfaisii\ModelStatus\HasStatuses;
use Spatie\Activitylog\Models\Activity;
use Spatie\QueryBuilder\AllowedFilter;


class Lead extends BaseModel
{
    use HasFactory, HasRecordCreator, HasStatuses, HasCalenderEvent;

    protected $fillable = [
        'title',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone_no',
        'dob',
        'post_code',
        'address',
        'is_marked_as_job',
        'job_type_id',
        'fuel_type_id',
        'surveyor_id',
        'lead_generator_id',
        'lead_source_id',
        'benefit_type_id',
        'comments',
        'created_by_id'
    ];

    protected $appends = ['full_name', 'status_details'];

    protected $casts = [
        'is_marked_as_job' => 'boolean'
    ];

    protected array $allowedIncludes = [
        'leadStatus',
        'leadGenerator',
        'statuses',
        'surveyBooking',
        'leadCustomerAdditionalDetail',
        'benefits',
        'callCenters',
        'callCenters.createdBy',
        'callCenters.callCenterStatus'
    ];

    protected array $discardedFieldsInFilter = [
        'post_code'
    ];

    public function scopeByRole($query, string $role, ?User $user = null)
    {
        $user ??= auth()->user();

        if ($user->hasRole($role)) {
            $assignedLeadGenerators = $user->leadGeneratorAssignments()->pluck('lead_generator_id');

            $query->whereIn('lead_generator_id', $assignedLeadGenerators);
        }

        return $query;
    }

    protected function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
    }

    protected function getStatusesAttribute()
    {
        return $this->statuses();
    }

    protected function getStatusDetailsAttribute()
    {
        $latest = $this->latestStatus();

        $latest['lead_status_model'] = LeadStatus::where('name', $latest->name)->first();

        return $latest;
    }

    protected function getExtraFilters(): array
    {
        return [
            AllowedFilter::custom('post_code', new FilterByPostcode()),
            AllowedFilter::custom('name', new FilterByName()),
            AllowedFilter::custom('statuses', new FilterByStatus()),
        ];
    }

    public function getLogsAttribute()
    {
        $lead = $this;

        return Activity::forSubject($this)
            ->orWhere(function ($query) use ($lead) {
                if (!is_null($lead->leadCustomerAdditionalDetail)) {
                    $query
                        ->where('subject_type', (new LeadCustomerAdditionalDetail())->getMorphClass())
                        ->where('subject_id', $lead->leadCustomerAdditionalDetail->id);
                }
            })
            ->orWhere(function ($query) use ($lead) {
                if (!is_null($lead->surveyBooking)) {
                    $query
                        ->where('subject_type', (new SurveyBooking())->getMorphClass())
                        ->where('subject_id', $lead->surveyBooking->id);
                }
            })
            ->with(['causer' => function ($query) {
                $query->select('id', 'name', 'created_at', 'updated_at');
            }])
            ->get();
    }

    public function jobType()
    {
        return $this->belongsTo(JobType::class);
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class);
    }

    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class);
    }

    public function surveyor()
    {
        return $this->belongsTo(Surveyor::class);
    }

    public function leadGenerator()
    {
        return $this->belongsTo(LeadGenerator::class);
    }

    public function secondReceipent()
    {
        return $this->hasOne(SecondReceipent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leadCustomerAdditionalDetail()
    {
        return $this->hasOne(LeadCustomerAdditionalDetail::class);
    }

    public function surveyBooking()
    {
        return $this->hasOne(SurveyBooking::class);
    }

    public function callCenters()
    {
        return $this->hasMany(CallCenter::class);
    }

    public function benefits()
    {
        return $this->belongsToMany(BenefitType::class, LeadHasBenefit::class)
            ->withPivot('created_by_id')
            ->withTimestamps();
    }
}
