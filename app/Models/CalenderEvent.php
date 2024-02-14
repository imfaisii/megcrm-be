<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Imfaisii\ModelStatus\HasStatuses;

class CalenderEvent extends BaseModel
{
    use HasFactory, HasRecordCreator, HasStatuses;

    protected array $allowedIncludes = [
        'createdBy'
    ];

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'all_day',
        'description',
        'location',
        'extra_data',
        'eventable_id',
        'eventable_type',
        'calendar_id',
        'user_id',
        'created_by_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'extra_data' => 'array',
    ];

    public function eventable()
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
