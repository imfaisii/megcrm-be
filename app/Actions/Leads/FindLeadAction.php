<?php

namespace App\Actions\Leads;

use App\Actions\Common\AbstractFindAction;
use App\Actions\Common\BaseModel;
use App\Enums\Users\MediaCollectionEnum;
use App\Models\Lead;

class FindLeadAction extends AbstractFindAction
{
    protected string $modelClass = Lead::class;

    public function findOrFail($primaryKey, array $columns = ['*']): BaseModel
    {
        $lead = parent::findOrFail($primaryKey);
        $lead['submission_documents'] = $lead->getMedia(MediaCollectionEnum::SUBMISSION_DOCUMENTS)->toArray();

        return $lead;
    }
}
