<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Filters\Leads\FilterByStatus;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Imfaisii\ModelStatus\HasStatuses;
use Spatie\Activitylog\Models\Activity;
use Spatie\QueryBuilder\AllowedFilter;

use function App\Helpers\shouldAppend;

class Lead extends BaseModel
{
    use HasFactory, HasRecordCreator, HasStatuses;

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

    protected array $allowedIncludes = [
        'leadGenerator',
    ];

    protected function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
    }

    protected function getStatusDetailsAttribute()
    {
        $data = $this->latestStatus();

        $data['user'] = User::find($data['user_id']);
        $data['lead_status'] = LeadStatus::where('name', $data['name'])->first();

        return $data;
    }

    protected function getExtraFilters(): array
    {
        return [
            AllowedFilter::custom('statuses', new FilterByStatus()),
        ];
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

    public function benefitType()
    {
        return $this->belongsTo(BenefitType::class);
    }

    public function leadAdditionalDetail()
    {
        return $this->hasOne(LeadAdditionalDetail::class);
    }

    public function secondReceipent()
    {
        return $this->hasOne(SecondReceipent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
