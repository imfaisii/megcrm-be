<?php

namespace App\Imports\Leads;

use App\Classes\LeadResponseClass;
use App\Enums\DataMatch\DataMatchEnum;
use App\Models\Lead;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use function App\Helpers\removeSpace;

class LeadDataMatchImport extends DefaultValueBinder implements ToCollection, WithHeadingRow
{

    public function __construct(public LeadResponseClass $classResponse)
    {
        //
    }

    public function headingRow(): int
    {
        return 3;
    }
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        try {
            $rows->transform(function($eachRow){

                    $eachRow['date_of_birth'] = (is_int($eachRow['date_of_birth'])
                    ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($eachRow['date_of_birth'])->format('Y-m-d')
                    : $eachRow['date_of_birth']);
                    $eachRow['date_uploaded'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($eachRow['date_uploaded'])->format('Y-m-d H:i:s');

                    $eachRow['date_processed_by_dwp']  = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($eachRow['date_processed_by_dwp'])->format('Y-m-d H:i:s');
                return $eachRow;
            });
            DB::transaction(function () use ($rows) {
                $rows->each(function ($eachLead) {
                    $lead = Lead::query()
                        ->withWhereHas('leadCustomerAdditionalDetail', function ($query) {
                            $query->where('datamatch_progress', DataMatchEnum::StatusSent);
                        })
                        ->with('leadAdditional')
                        ->where([
                            ['last_name', '=', $eachLead['surname']],
                            ['first_name', '=', $eachLead['forename']],
                            [
                                'dob',
                                '=',
                                (is_int($eachLead['date_of_birth'])
                                    ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($eachLead['date_of_birth'])->format('Y-m-d')
                                    : $eachLead['date_of_birth'])
                            ],
                            ['post_code', '=', strtoupper(removeSpace($eachLead['postcode']))],
                        ])->get();
                    if ($lead->count() > 1) {
                        //means multiple records found now need to query more for specific
                        $response = $lead->filter(function ($item) use ($eachLead) {
                            return stripos($item, $eachLead['address']) !== false;
                        })->first();
                        $response->leadCustomerAdditionalDetail->update([
                            'datamatch_progress' => DataMatchEnum::StatusReceived,
                        ]);
                        Log::channel('data_match_result_file_read_log')->info("Data Match updated for " . json_encode($response->toArray()) . " against " . json_encode($eachLead->toArray()));
                        // $response->leadAdditional()->update([]);
                    } else if ($lead->count() == 1) {
                        $lead->leadCustomerAdditionalDetail->update([
                            'datamatch_progress' => DataMatchEnum::StatusReceived,
                        ]);
                        Log::channel('data_match_result_file_read_log')->info("Data Match updated for " . json_encode($lead->toArray()) . " against " . json_encode($eachLead->toArray()));

                        // $lead->leadAdditional()->update([]);

                        //exact one found just update it
                    } else {
                        //no found
                        Log::channel('data_match_result_file_read_log')->error('No match found for record' . json_encode($eachLead->ToArray()));
                        $this->classResponse->failedLeads[] = $eachLead->toArray();
                    }

                });

            });

        } catch (Exception $e) {
            Log::channel('data_match_result_file_read_log')->info(
                "Error importing data match result:  " . $e->getMessage()
            );
        }

    }
}
