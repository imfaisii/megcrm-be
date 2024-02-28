<?php

namespace App\Actions\InstallationTypes;

use App\Actions\Common\AbstractDeleteAction;
use App\Models\InstallationType;

class DeleteInstallationTypeAction extends AbstractDeleteAction
{
    protected string $modelClass = InstallationType::class;
}
