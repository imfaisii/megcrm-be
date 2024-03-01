<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstallationTypeHasMeasure extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'installation_type_id',
        'measure_id',
        'created_by_id'
    ];

    protected array $allowedIncludes = [
        'createdBy'
    ];

    public function installationType()
    {
        return $this->belongsTo(InstallationType::class);
    }

    public function measure()
    {
        return $this->belongsTo(Measure::class);
    }
}
