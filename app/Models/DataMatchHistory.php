<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataMatchHistory extends BaseModel
{
    use HasFactory;


    protected $fillable = [
        'datamatch_progress',
        'datamatch_progress_date',
        'lead_id',
        'dob',
        'urn',
        'address',
        'post_code',
        'data_match_sent_date',
        'first_name',
        'last_name',
        'middle_name'
    ];

    /**
     * Get the user that owns the DataMatchHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'id');
    }

}
