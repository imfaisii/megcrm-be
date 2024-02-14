<?php

namespace App\Actions\CalendereEvent;

use App\Actions\Common\AbstractListAction;
use App\Models\CalenderEvents;

class StoreCalenderEventAction extends AbstractListAction
{
  protected string $modelClass = CalenderEvents::class;

  public function create(array $data){
    
  }
}
