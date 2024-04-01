<?php

namespace App\Actions\Companies;

use App\Actions\Common\AbstractDeleteAction;
use App\Models\Company;

class DeleteCompanyAction extends AbstractDeleteAction
{
    protected string $modelClass = Company::class;
}
