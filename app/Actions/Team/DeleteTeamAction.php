<?php

namespace App\Actions\Team;

use App\Actions\Common\AbstractDeleteAction;
use App\Models\Team;
use App\Models\User;

class DeleteTeamAction extends AbstractDeleteAction
{
    protected string $modelClass = Team::class;

    public function delete($model): ?bool
    {
        $model->users()->detach();

        return parent::delete($model);
    }
}
