<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MobileAssetSync extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'asset_id',
        'lead_id',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
