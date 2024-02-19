<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadCustomerAdditionalDetail extends BaseModel
{
    use HasFactory;

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
        'lead_id'
    ];

    protected $casts = [
        'is_datamatch_required' => 'boolean',
        'is_customer_owner' => 'boolean',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
