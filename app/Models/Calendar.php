<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Calendar extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'name',
        'color'
    ];
}
