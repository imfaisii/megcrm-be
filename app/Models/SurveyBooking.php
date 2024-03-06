<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasCalenderEvent;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SurveyBooking extends BaseModel
{
    use HasFactory, HasRecordCreator, HasCalenderEvent;

    protected $fillable = [
        'surveyor_id',
        'survey_at',
        'preffered_time',
        'comments',
        'lead_id',
    ];

    public function surveyor()
    {
        return $this->belongsTo(User::class, 'surveyor_id', 'id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
