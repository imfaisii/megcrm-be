<?php

namespace App\Actions\InstallationTypes;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\InstallationType;

class UpdateInstallationTypeAction extends AbstractUpdateAction
{
    protected string $modelClass = InstallationType::class;

    public function update(mixed $installationType, array $data): mixed
    {
        /** @var InstallationType $installationType */
        $installationType = parent::update($installationType, $data);

        // update or create relations
        (new StoreInstallationTypeAction())->relations($installationType, $data);

        return $installationType;
    }
}
