<?php

namespace App\Actions\Leads;

use App\Actions\Common\AbstractFindAction;
use App\Actions\Common\BaseModel;
use App\Enums\AppEnum;
use App\Enums\Users\MediaCollectionEnum;
use App\Http\Controllers\File\FileHanlderController;
use App\Http\Requests\File\GetFilesRequest;
use App\Models\Lead;
use Exception;
use Illuminate\Support\Facades\Log;

use function App\Helpers\meg_encrypt;

class FindLeadAction extends AbstractFindAction
{
    protected string $modelClass = Lead::class;

    public function findOrFail($primaryKey, array $columns = ['*']): BaseModel
    {
        $lead = parent::findOrFail($primaryKey);
        $lead['submission_documents'] = $lead->getMedia(MediaCollectionEnum::SUBMISSION_DOCUMENTS)->toArray();
        $lead['customer_support_images'] = $this->setCustomerFilesLead($lead);
        $lead['customer_support_documents'] = $this->setCustomerFilesLead($lead, AppEnum::CUSTOMER_LEAD_DOCUMENTS);
        $lead['customer_support_images_ids'] = $this->setCustomerFilesLead($lead,fileTypes:'ids');
        $lead['customer_support_documents_ids'] = $this->setCustomerFilesLead($lead, AppEnum::CUSTOMER_LEAD_DOCUMENTS,fileTypes:'ids');
        return $lead;
    }
    /**
     * Sets the customer files associated with the lead.
     *
     * @param Lead $lead The lead object.
     * @param string|null $type The type of files to retrieve. Defaults to 'images'.
     *
     * @return array An associative array of customer files associated with the lead.
     *
     * @throws Exception If an error occurs while retrieving the files.
     */
    private function setCustomerFilesLead($lead, $type = null,$fileTypes = 'files'): array
    {
        try {

            $type ??= AppEnum::CUSTOMER_LEAD_IMAGES;
            // Create an instance of the controller
            $filesController = new FileHanlderController();

            // Create an instance of the request
            $request = new GetFilesRequest();
            $request->merge([
                'type' => $fileTypes,
                'collection_name' => $type // Assuming $type is defined elsewhere
            ]);
            $response = $filesController->getAllFilesAssocaiatedWithModel(meg_encrypt("Lead"), meg_encrypt($lead->id), $request);

            return data_get($response->getData(true), 'data', []);

        } catch (Exception $e) {
            Log::channel('slack_exceptions')->info(__FUNCTION__ . " agaisnt user " . auth()->user()->email . " with exception " . $e->getMessage());
            return [];
        }
    }
}
