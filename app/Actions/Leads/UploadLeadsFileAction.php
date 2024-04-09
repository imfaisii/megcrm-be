<?php

namespace App\Actions\Leads;

use App\Classes\LeadResponseClass;
use App\Http\Requests\DataMatch\UploadDataMatchRequest;
use App\Imports\Leads\LeadDataMatchImport;
use App\Imports\Leads\LeadsImport;
use App\traits\Jsonify;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class UploadLeadsFileAction
{
    use Jsonify;

    public function __construct(public LeadResponseClass $leadResponseClass)
    {
    }

    public function execute(Request $request): JsonResponse
    {
        try {
            $exampleHeader = [
                'website',
                'name',
                'email',
                'contact_number',
                'dob',
                'postcode',
                'address',
                'what_is_your_home_ownership_status',
                'benefits',
            ];
            $this->CheckFileHeaderErrors($exampleHeader, $request->file('file'));
            Excel::import(new LeadsImport($this->leadResponseClass), $request->file('file'));

            return $this->success('File was uploaded successfully.', data: $this->leadResponseClass);
        } catch (Exception $e) {
            Log::channel('lead_file_read_log')->info(
                'Error importing exception '.$e->getMessage()
            );

            return $this->error($e->getMessage());
        }
    }


    public function executeLeadsDataMatchResultUpload(UploadDataMatchRequest $request)
    {
        try {
            $exampleHeader = [
                "landlord_surname",
                "landlord_forename",
                "surname",
                "forename",
                "date_of_birth",
                "property_name_or_number",
                "address_line_1",
                "address_line_2",
                "address_line_3",
                "town",
                "county",
                "postcode",
                "urn",
                "eco_3_verification_status",
                "eco_4_verification_status",
                "owner_status",
                "date_uploaded",
                "date_processed_by_dwp",
            ];

            $this->CheckFileHeaderErrors($exampleHeader, $request->file('file'), 3);
            Excel::import(new LeadDataMatchImport($this->leadResponseClass), $request->file('file'));

            return $this->success('File was uploaded successfully.', data: $this->leadResponseClass);
        } catch (Exception $e) {
            Log::channel('data_match_result_file_read_log')->info(
                "Error importing exception " . $e->getMessage()
            );
            return $this->error($e->getMessage());
        }
    }


    private function CheckFileHeaderErrors(array $headersArray, $file, $headingRow = 1)
    {
        $headings = (new HeadingRowImport($headingRow))->toArray($file)[0][0];


        if (count($headings) < count($headersArray)) {

            throw new Exception('File has invalid header. (less headings)' . json_encode($headings));
        }

        $headings = array_map('strtolower', $headings);

        $headerDifference = array_diff($headersArray, $headings);


        throw_if(filled($headerDifference), new Exception('File has invalid header ( not matched ).' . json_encode($headerDifference)));

    }
}
