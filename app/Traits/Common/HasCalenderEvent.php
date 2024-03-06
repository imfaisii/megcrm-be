<?php

namespace App\Traits\Common;

use App\Models\CalenderEvent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasCalenderEvent
{
    public function event(): MorphMany
    {
        return $this->morphMany(CalenderEvent::class, 'eventable');
    }

    public function user(): HasMany
    {
        return $this->hasMany(CalenderEvent::class, 'user_id');
    }

    public function latestEvent(): MorphOne
    {
        return $this->morphOne(CalenderEvent::class, 'eventable')->latestOfMany();
    }
}
