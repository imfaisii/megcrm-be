<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Submission extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'area',
        'pre_rating',
        'post_rating',
        'lead_id'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
