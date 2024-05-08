<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Filters\CalendarEvents\FilterByCalendars;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Imfaisii\ModelStatus\HasStatuses;
use Spatie\QueryBuilder\AllowedFilter;

class CalenderEvent extends BaseModel
{
    use HasFactory, HasRecordCreator, HasStatuses;

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
        'notification',
        'calendar_id',
        'user_id',
        'created_by_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'extra_data' => 'array',
        'all_day' => 'boolean',
        'notification' => 'json',
    ];

    protected array $allowedIncludes = [
        'calendar',
        'eventable.lead',
        'createdBy',
    ];

    protected function getExtraFilters(): array
    {
        return [
            AllowedFilter::custom('calendars', new FilterByCalendars()),
        ];
    }

    public function calendar()
    {
        return $this->belongsTo(Calendar::class);
    }

    public function scopeCurrentUser($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function eventable()
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
