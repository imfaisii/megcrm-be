<?php

namespace App\Cache;

use Illuminate\Support\Facades\Cache;

class AirCallContactCreationCache extends AbstractCacheClass
{
    public const version = '1.0-';
    public const defaultKey = 'MegCRM_air_call_contact-';
    public function getKey(string $key)
    {
        return self::defaultKey . self::version . $key;
    }


}
