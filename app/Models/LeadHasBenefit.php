<?php

namespace App\Models;

use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadHasBenefit extends Model
{
    use HasFactory, HasRecordCreator;

    protected $filleble = [
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
