<?php

namespace App\Traits\Common;

use Spatie\Activitylog\Models\Activity;

use function App\Helpers\is_append_present;

trait HasLogsAppend
{
    public static function bootHasLogsAppend()
    {
        //! TABLE IS REQUIRED TO AVOID GETTING RECURSIVE LOGS
        static::retrieved(function ($model) {
            if (is_append_present("{$model->getTable()}_logs")) {
                $model->append('logs');
            }
        });
    }

    public function getLogsAttribute()
    {
        $modelInstance = $this->getModel();

        return Activity::where([
            'subject_type' => get_class($modelInstance),
            'subject_id' => $this->id
        ])
            ->latest()
            ->with(['causer' => function ($query) {
                $query->select('id', 'name', 'created_at', 'updated_at');
            }])
            ->get();
    }
}
