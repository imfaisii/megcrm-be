<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAdditional extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'gender',
        'dob',
        'phone_no',
        'address',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
