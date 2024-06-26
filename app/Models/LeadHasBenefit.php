<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadHasBenefit extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'lead_id',
        'benefit_type_id',
    ];

    public function lead()
    {
        return $this->belongsToMany(Lead::class);
    }

    public function benefitType()
    {
        return $this->belongsToMany(BenefitType::class);
    }
}
