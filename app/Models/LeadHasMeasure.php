<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadHasMeasure extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'lead_id',
        'measure_id',
    ];

    public function lead()
    {
        return $this->belongsToMany(Lead::class);
    }

    public function measure()
    {
        return $this->belongsToMany(Measure::class);
    }
}
