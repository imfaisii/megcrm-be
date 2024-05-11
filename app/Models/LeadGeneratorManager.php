<?php

namespace App\Models;

use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadGeneratorManager extends Model
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'user_id',
        'lead_generator_id'
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
