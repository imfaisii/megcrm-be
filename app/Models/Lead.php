<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Enums\AppEnum;
use App\Enums\Permissions\RoleEnum;
use App\Filters\Leads\FilterByBookedBy;
use App\Filters\Leads\FilterByName;
use App\Filters\Leads\FilterByPostcode;
use App\Filters\Leads\FilterByStatus;
use App\Filters\Leads\FilterBySurveyor;
use App\Notifications\Customer\CustomerLeadTrackingMail;
use App\Traits\Common\HasCalenderEvent;
use App\Traits\Common\HasRecordCreator;
use App\Traits\HasTeamTrait;
use BeyondCode\Comments\Traits\HasComments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Imfaisii\ModelStatus\HasStatuses;
use Spatie\Activitylog\Models\Activity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\QueryBuilder\AllowedFilter;

use function App\Helpers\meg_encrypt;

class Lead extends BaseModel implements HasMedia
{
    use HasCalenderEvent,
        HasComments,
        HasFactory,
        HasRecordCreator,
        HasStatuses,
        HasTeamTrait,
        InteractsWithMedia,
        Notifiable;

    public $ScopeColumn = 'surveyor_id';

    protected $fillable = [
        'title',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone_no',
        'dob',
        'post_code',
        'actual_post_code',
        'plain_address',
        'city',
        'county',
        'country',
        'address',
        'epc',
        'recommend',
        'gas_safe',
        'is_marked_as_job',
        'has_second_receipent',
        'job_type_id',
        'fuel_type_id',
        // 'surveyor_id',
        'lead_generator_id',
        'lead_source_id',
        'benefit_type_id',
        'notes',
        'created_by_id',
        'sub_building',
        'building_number',
        'reference_number',
        'raw_api_response',

    ];

    protected array $allowedAppends    = ['status_details', 'phone_number_formatted'];

    protected $appends = ['full_name', 'status_details', 'phone_number_formatted'];

    protected $casts = [
        'is_marked_as_job' => 'boolean',
        'has_second_receipent' => 'boolean',
        'raw_api_response' => 'json',
    ];

    protected array $allowedIncludes = [
        'leadStatus',
        'leadGenerator',
        'statuses',
        'surveyBooking',
        'installationBookings',
        'leadCustomerAdditionalDetail',
        'benefits',
        'measures',
        'callCenters',
        'callCenters.createdBy',
        'callCenters.callCenterStatus',
        'comments.commentator',
        'leadAdditional',
        'notifications',
        'secondReceipent',
        'submission',
        'mobileAssetSyncs',
    ];

    protected array $discardedFieldsInFilter = [
        'post_code',
    ];

    protected function routeNotificationForTwilio()
    {
        return $this->phone_number_formatted;
    }

    public function scopeByRole($query, string $role, ?User $user = null)
    {
        $user ??= auth()->user();

        if ($user->hasRole($role) && $role === RoleEnum::SURVEYOR) {
            $assignedLeadGenerators = $user->leadGeneratorAssignments()->pluck('lead_generator_id');
            $query->where(function ($query) use ($assignedLeadGenerators, $user) {
                $query->whereIn('lead_generator_id', $assignedLeadGenerators);  // if we pass an empty array the condition become 0=1

                $query->orWhereHas('surveyBooking', function ($query) use ($user) {
                    $query->where('surveyor_id', $user->id);
                });
            });
        }

        return $query;
    }

    protected function getPhoneNumberFormattedAttribute()
    {
        if (!$this->phone_no || str()->length($this->phone_no) < 10) {
            return null;
        }

        return '+44' . substr($this->phone_no, -10);
    }

    protected function getFullNameAttribute()
    {
        return str_replace('  ', ' ', $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    protected function getStatusesAttribute()
    {
        return $this->statuses();
    }

    public function comments()
    {
        return $this->morphMany(config('comments.comment_class'), 'commentable')->latest();
    }

    protected function getStatusDetailsAttribute()
    {
        $latest = $this->latestStatus();

        if (!is_null($latest)) {
            $latest['lead_status_model'] = LeadStatus::where('name', $latest->name)->first();
        } else {
            $latest['lead_status_model'] = null;
        }

        $latest['survey_booked'] = $this->latestStatus('Survey Booked');

        return $latest;
    }

    protected function getExtraFilters(): array
    {
        return [
            AllowedFilter::custom('post_code', new FilterByPostcode()),
            AllowedFilter::custom('name', new FilterByName()),
            AllowedFilter::custom('statuses', new FilterByStatus()),
            AllowedFilter::custom('surveyor_id', new FilterBySurveyor()),
            AllowedFilter::custom('survey_booked_by', new FilterByBookedBy()),
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
            ->orWhere(function ($query) use ($lead) {
                if (!is_null($lead->leadAdditional)) {
                    $query
                        ->where('subject_type', (new LeadAdditional())->getMorphClass())
                        ->where('subject_id', $lead->leadAdditional->id);
                }
            })
            ->with([
                'causer' => function ($query) {
                    $query->select('id', 'name', 'created_at', 'updated_at');
                },
            ])
            ->get();
    }

    public function leadAdditional()
    {
        return $this->hasOne(LeadAdditional::class);
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
        return $this->belongsTo(User::class, 'surveyor_id', 'id');
    }

    public function leadGenerator()
    {
        return $this->belongsTo(LeadGenerator::class);
    }

    public function secondReceipent()
    {
        return $this->hasOne(SecondReceipent::class);
    }

    public function submission()
    {
        return $this->hasOne(Submission::class);
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

    public function installationBookings()
    {
        return $this->hasMany(InstallationBooking::class);
    }

    public function callCenters()
    {
        return $this->hasMany(CallCenter::class);
    }

    public function mobileAssetSyncs()
    {
        return $this->hasMany(MobileAssetSync::class);
    }

    public function benefits()
    {
        return $this->belongsToMany(BenefitType::class, LeadHasBenefit::class)
            ->withPivot('created_by_id')
            ->withTimestamps();
    }

    public function measures()
    {
        return $this->belongsToMany(Measure::class, LeadHasMeasure::class)
            ->withPivot('created_by_id')
            ->withTimestamps();
    }


    public function sendStatusEmailToCustomer()
    {
        $time = now()->addDays(AppEnum::LEAD_TRACKNG_DAYS_ALLOWED);
        $lead = $this;
        $encryptedID = meg_encrypt($lead->id);
        $encryptedModel = meg_encrypt('Lead');
        $route = URL::temporarySignedRoute('customer.lead-status', $time, ['lead' => $encryptedID]);
        $request = Request::create($route);
        $routeForFiles = URL::temporarySignedRoute('file_upload', $time, ['ID' => $encryptedID, 'Model' => $encryptedModel]);
        $requestForFilesUpload = Request::create($routeForFiles);
        $requestForFilesDelete = Request::create(URL::temporarySignedRoute('file_delete', $time, ['ID' => $encryptedID, 'Model' => $encryptedModel]));

        $requestForFilesData = Request::create(URL::temporarySignedRoute('file_data', $time, ['ID' => $encryptedID, 'Model' => $encryptedModel]));
        $requestForSupport = Request::create(URL::temporarySignedRoute('customer.support-email', $time, ['ID' => $encryptedID]));

        $requestForFiles = Request::create($route);

        $lead->notify((new CustomerLeadTrackingMail([
            ...$request->query(),
            'lead' => $encryptedID,
            'model' => $encryptedModel,
            'SignatureForUpload' => $requestForFilesUpload->query('signature'),
            'SignatureForDelete' => $requestForFilesDelete->query('signature'),
            'SignatureForData' => $requestForFilesData->query('signature'),
            'SignatureForSupport' => $requestForSupport->query('signature'),

        ])));
        return $this;
    }
}
