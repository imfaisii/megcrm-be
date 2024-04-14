<?php

namespace App\Actions\Customer;

use App\Actions\Common\AbstractFindAction;
use App\Actions\Common\BaseModel;
use App\Models\Lead;

use function App\Helpers\meg_encrypt;

class ListCustomerLeadStatusAction extends AbstractFindAction
{
    protected string $modelClass = Lead::class;

    /**
     * @param  array|string[]  $columns
     */
    public function findOrFail($primaryKey, array $columns = ['*']): BaseModel
    {
        $lead = $this->getQuery()->findOrFail($primaryKey, [
            'title',
            'first_name',
            'last_name',
            'middle_name',
            'plain_address',
            'post_code',
            'address',
            'email',
            'phone_no',
            'dob',
            'country',
            'county',
            'city',
            'created_at',
            'updated_at',
            'reference_number',
            'raw_api_response',
            'building_number',
            'sub_building',
        ])?->setAppends([]);
        if (filled($lead)) {
            $lead->encryptedId = meg_encrypt($primaryKey);
        }

        return $lead;
    }
}
