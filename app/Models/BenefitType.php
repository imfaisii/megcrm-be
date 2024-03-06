<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BenefitType extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'name',
        'created_by_id',
    ];

    protected array $allowedIncludes = [
        'createdBy',
    ];

    public function leads()
    {
        return $this->belongsToMany(Lead::class, LeadHasBenefit::class)
            ->withPivot('created_by_id')
            ->withTimestamps();
    }
}
