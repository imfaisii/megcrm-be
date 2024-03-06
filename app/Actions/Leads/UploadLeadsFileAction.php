<?php

namespace App\Actions\Leads;

use App\Classes\LeadResponseClass;
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

            $headings = (new HeadingRowImport())->toArray($request->file('file'))[0][0];

            if (count($headings) < 8) {
                throw new Exception('File has invalid header. (less headings)'.json_encode($headings));
            }

            $headings = array_map('strtolower', $headings);

            $headerDifference = array_diff($exampleHeader, $headings);

            throw_if(filled($headerDifference), new Exception('File has invalid header ( not matched ).'.json_encode($headerDifference)));

            Excel::import(new LeadsImport($this->leadResponseClass), $request->file('file'));

            return $this->success('File was uploaded successfully.', data: $this->leadResponseClass);
        } catch (Exception $e) {
            Log::channel('lead_file_read_log')->info(
                'Error importing exception '.$e->getMessage()
            );

            return $this->error($e->getMessage());
        }
    }
}
