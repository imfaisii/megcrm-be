<?php

namespace App\Traits\Common;

use App\Models\CalenderEvents;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasCalenderEvent
{


  public function event(): MorphMany
  {
    return $this->morphMany(CalenderEvents::class, 'eventable');
  }

  public function user(): HasMany
  {
    return $this->hasMany(CalenderEvents::class, 'user_id');
  }

  /**
   * Get all of latest event added 
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasOne
   */
  public function latestEvent(): MorphOne
  {
    return $this->morphOne(CalenderEvents::class, 'eventable')->latestOfMany();
  }
}
  