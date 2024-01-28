<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadAdditionalDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'adress_line_1',
        'adress_line_2',
        'adress_line_3',
        'adress_line_4',
        'latitude',
        'longitude',
        'thorough_fare',
        'building_name',
        'building_number',
        'sub_building_name',
        'sub_building_number',
        'locality',
        'town_or_city',
        'county',
        'country',
        'district',
        'residential',
        'lead_id'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
