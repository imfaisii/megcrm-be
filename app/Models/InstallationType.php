<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstallationType extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'name',
        'created_by_id'
    ];

    protected array $allowedIncludes = [
        'installationTypeHasMeasures',
        'createdBy'
    ];

    public function installationTypeHasMeasures()
    {
        return $this->belongsToMany(Measure::class, InstallationTypeHasMeasure::class)
            ->withPivot('created_by_id')
            ->withTimestamps();
    }
}
