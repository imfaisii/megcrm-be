<?php

namespace App\Traits\Common;

trait HasFormattedDatesAppend
{
    public static function bootHasFormattedDatesAppend()
    {
        static::retrieved(function ($model) {
            $model->append('created_at_formatted');
            $model->append('updated_at_formatted');
        });
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at->diffForHumans();
    }
}
