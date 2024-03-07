<?php

namespace App\Fascade;

use App\Classes\AirCall;
use Illuminate\Support\Facades\Facade;

class AirCallFascade extends Facade
{
    protected static function getFacadeAccessor()
    {
        self::clearResolvedInstance(AirCall::class);

        return AirCall::class;
    }
}
