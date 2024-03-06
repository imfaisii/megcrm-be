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
        'visa_expiry',
        'account_number',
        'nin',
        'bank_id',
        'user_id',
    ];

    protected array $allowedIncludes = [
        'bank',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
