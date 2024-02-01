<?php

namespace App\Traits\Common;

use Spatie\Activitylog\Models\Activity;

use function App\Helpers\is_append_present;

trait HasLogsAppend
{
    public static function bootHasLogsAppend()
    {
        static::retrieved(function ($model) {
            if (is_append_present('logs')) {
                $model->append('logs');
            }
        });
    }

    public function getLogsAttribute()
    {
        return Activity::all();
    }
}
