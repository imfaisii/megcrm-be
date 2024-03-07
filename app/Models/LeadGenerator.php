<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadGenerator extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'name',
        'sender_id',
        'mask_name',
    ];

    protected array $allowedIncludes = [
        'createdBy',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function leadGeneratorAssignments()
    {
        return $this->belongsToMany(User::class, LeadGeneratorAssignment::class)
            ->withPivot('created_by_id')
            ->withTimestamps();
    }
}
