<?php

namespace App\Actions\Team;

use App\Actions\Common\AbstractFindAction;
use App\Actions\Common\BaseModel;
use App\Enums\Users\MediaCollectionEnum;
use App\Models\Team;
use Exception;
use Illuminate\Support\Facades\Http;

class FindTeamAction extends AbstractFindAction
{
    protected string $modelClass = Team::class;


    public function findOrFail($primaryKey, array $columns = ['*']): BaseModel
    {
        $team = $this->getQuery()->with('users')->findOrFail($primaryKey, $columns);
        return $team;
    }
}
