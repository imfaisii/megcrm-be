<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Models\Lead;
use App\Models\Surveyor;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SurveyBooking extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'surveyor_id',
        'survey_at',
        'preffered_time',
        'comments',
        'lead_id'
    ];

    public function surveyor()
    {
        return $this->belongsTo(Surveyor::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
