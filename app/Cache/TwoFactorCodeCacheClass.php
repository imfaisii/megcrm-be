<?php

namespace App\Cache;


class TwoFactorCodeCacheClass extends AbstractCacheClass
{

    public function __construct()
    {
        parent::__construct('file');
    }
    public const cacheTime = 300; //seconds
    public const version = '1.0-';
    public const defaultKey = 'MegCRM_two_factor_code-';
    public function getKey(string $key)
    {
        return self::defaultKey . self::version . $key;
    }


}
