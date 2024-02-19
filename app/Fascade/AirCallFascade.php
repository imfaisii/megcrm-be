<?php

namespace App\Fascade;

use Illuminate\Support\Facades\Facade;

class AirCallFascade extends Facade
{
  protected static function getFacadeAccessor()
  {
    return 'AirCall';
  }
}
