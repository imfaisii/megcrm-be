<?php

namespace App\Actions\Companies;

use App\Actions\Common\AbstractListAction;
use App\Models\Company;

class ListCompanyAction extends AbstractListAction
{
    protected string $modelClass = Company::class;
}
