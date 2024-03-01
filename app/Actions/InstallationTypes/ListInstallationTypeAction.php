<?php

namespace App\Actions\InstallationTypes;

use App\Actions\Common\AbstractListAction;
use App\Models\InstallationType;

class ListInstallationTypeAction extends AbstractListAction
{
    protected string $modelClass = InstallationType::class;
}
