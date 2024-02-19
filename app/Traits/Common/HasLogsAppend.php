<?php

namespace App\Traits\Common;

use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

use function App\Helpers\get_all_appends;
use function App\Helpers\is_append_present;

trait HasLogsAppend
{
    public static function bootHasLogsAppend()
    {
        //! TABLE IS REQUIRED TO AVOID GETTING RECURSIVE LOGS
        static::retrieved(function ($model) {

            $appends = get_all_appends();

            if (is_append_present("{$model->getTable()}_logs")) {
                $model->append('logs');
            }

            foreach ($appends as $append) {
                if (!Str::endsWith($append, ['_logs'])) {
                    $model->append($append);
                }
            }
        });
    }

    public function getLogsAttribute()
    {
        $dispatcher = Activity::getEventDispatcher();
        Activity::unsetEventDispatcher();

        $logs = Activity::forSubject($this)->latest()
            ->with(['causer' => function ($query) {
                $query->select('id', 'name', 'created_at', 'updated_at');
            }])
            ->get();

        Activity::setEventDispatcher($dispatcher);

        return $logs;
    }
}
