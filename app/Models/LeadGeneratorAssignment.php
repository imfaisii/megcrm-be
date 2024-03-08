<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadGeneratorAssignment extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'user_id',
        'lead_generator_id',
    ];

    protected array $allowedIncludes = [
        'createdBy',
        'user',
        'leadGenerator',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leadGenerator()
    {
        return $this->belongsTo(LeadGenerator::class);
    }
}
