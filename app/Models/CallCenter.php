<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CallCenter extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'called_at',
        'comments',
        'lead_id',
        'call_center_status_id'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function callCenterStatus()
    {
        return $this->belongsTo(CallCenterStatus::class);
    }
}
