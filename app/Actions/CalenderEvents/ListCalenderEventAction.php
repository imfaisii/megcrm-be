<?php

namespace App\Actions\CalenderEvents;

use App\Actions\Common\AbstractListAction;
use App\Models\CalenderEvent;
use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder as SpatieQueryBuilder;
use Illuminate\Database\Eloquent\Builder;

class ListCalenderEventAction extends AbstractListAction
{
    protected string $modelClass = CalenderEvent::class;

    protected ?User $user = null;

    public function setUser(?User $user): ListCalenderEventAction
    {
        $this->user = $user;
        return $this;
    }

    public function getQuery(): SpatieQueryBuilder|Builder
    {
        $query = parent::getQuery();

        if (!is_null($this->user)) {
            $query->currentUser();
        }

        return $query;
    }
}
