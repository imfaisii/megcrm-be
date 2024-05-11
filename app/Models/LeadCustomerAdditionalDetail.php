<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use LaracraftTech\LaravelDateScopes\DateScopes;

class LeadCustomerAdditionalDetail extends BaseModel
{
    use HasFactory, DateScopes;

    const CREATED_AT = 'updated_at';

    protected $fillable = [
        'contact_method',
        'priority_type',
        'time_to_contact',
        'time_at_address',
        'is_customer_owner',
        'is_lead_shared',
        'is_datamatch_required',
        'datamatch_progress',
        'datamatch_progress_date',
        'lead_id',
        'urn',
        'data_match_sent_date',
        'result_first_name',
        'result_last_name',
    ];

    protected $casts = [
        'is_datamatch_required' => 'boolean',
        'is_customer_owner' => 'boolean',
        'data_match_sent_date' => 'datetime'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /* overriding the default for getting log of each datamatch update */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This record has been {$eventName}")
            ->logAll()
            ->logOnly([
                'datamatch_progress',
                'urn',
                'data_match_sent_date',
                'datamatch_progress_date',
                'result_first_name',
                'result_last_name'
            ])
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }
}
