<?php

namespace App\Actions\Companies;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\Company;

class UpdateCompanyAction extends AbstractUpdateAction
{
    protected string $modelClass = Company::class;
}
