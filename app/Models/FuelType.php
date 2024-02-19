<?php

namespace App\Models;

use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Actions\Common\BaseModel;

class FuelType extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'name'
    ];

    protected array $allowedIncludes = [
        'createdBy'
    ];
}
