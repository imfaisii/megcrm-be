<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserHasInstallationType extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'user_id',
        'installation_engineer_type_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function installationType()
    {
        return $this->belongsTo(InstallationType::class);
    }
}
