<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstallationBooking extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'installer_id',
        'installation_at',
        'measure_id',
        'comments',
        'lead_id',
    ];

    public function measure()
    {
        return $this->belongsTo(Measure::class);
    }

    public function installer()
    {
        return $this->belongsTo(User::class, 'installer_id', 'id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
