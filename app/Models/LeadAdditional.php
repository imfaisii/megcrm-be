<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadAdditional extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'datamatch_confirmed',
        'land_registry_confirmed',
        'proof_of_address_confirmed',
        'epr_report_confirmed',
        'created_by_id',
        'lead_id',
    ];

    protected $casts = [
        'datamatch_confirmed' => 'boolean',
        'land_registry_confirmed' => 'boolean',
        'proof_of_address_confirmed' => 'boolean',
        'epr_report_confirmed' => 'boolean',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
