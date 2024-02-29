<?php

namespace App\Traits;

use function App\Helpers\formatCommas;
use function App\Helpers\removeSpace;

trait LeadAddressTrait
{
    public static function bootLeadAddressTrait()
    {
        static::saving(function ($model) {
            $model->address = strtolower(formatCommas($model->address));
            $model->post_code = strtolower(removeSpace($model->post_code));
        });

        // static::updating(function ($model) {
        //     $model->address = strtolower(formatCommas($model->address));
        //     $model->post_code = strtolower(removeSpace($model->post_code));
        //     $model->save();
        // });
    }
}
