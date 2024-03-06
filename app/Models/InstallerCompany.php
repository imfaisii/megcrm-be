<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstallerCompany extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'name',
        'address',
        'company_number',
        'vat_number',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
