<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Complaints extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'propsed_date',
        'status',
        'priority',
        'measure_id',
        'created_by_id',
        'complaintable_type',
        'complaintable_id',
    ];

    protected array $allowedIncludes = [
        'complaintable',
        'comments'
    ];


    public function complaintable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get all of the comments for the Complaints
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ComplaintComments::class, 'complain_id');
    }

    /**
     * Get the associated complaint measure
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function measure(): BelongsTo
    {
        return $this->belongsTo(Measure::class, 'measure_id');
    }
}
