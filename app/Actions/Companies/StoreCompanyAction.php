<?php

namespace App\Actions\Companies;

use App\Actions\Common\AbstractCreateAction;
use App\Models\Company;

class StoreCompanyAction extends AbstractCreateAction
{
    protected string $modelClass = Company::class;

    public function create(array $data): Company
    {
        $data['created_by_id'] = auth()->id();

        return parent::create($data);
    }
}
