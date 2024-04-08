<?php

namespace App\Actions\Companies;

use App\Actions\Common\AbstractListAction;
use App\Actions\Common\BaseModel;
use App\Enums\Users\MediaCollectionEnum;
use App\Models\Company;

class FindCompanyAction extends AbstractListAction
{
    protected string $modelClass = Company::class;

    public function findOrFail($primaryKey, array $columns = ['*']): BaseModel
    {
        $company = $this->getQuery()->findOrFail($primaryKey, $columns);
        $company['documents'] = $company->getMedia(MediaCollectionEnum::DOCUMENTS)->toArray();

        return $company;
    }
}
