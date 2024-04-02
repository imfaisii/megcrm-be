<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Company extends BaseModel implements HasMedia
{
    use HasFactory, HasRecordCreator, InteractsWithMedia;

    protected $fillable = [
        'name',
        'address',
        'number',
        'vat_number',
        'policy_reference',
        'public_liability_number'
    ];

    protected array $allowedIncludes = [
        'createdBy'
    ];
}
