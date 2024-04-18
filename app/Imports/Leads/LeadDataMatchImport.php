<?php

namespace App\Imports\Leads;

use App\Classes\LeadResponseClass;
use App\Models\Lead;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
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
     * @param  Collection  $collection
     */
    public function collection(Collection $rows)
    {
        try {
            $rows = $rows->transform(function ($eachRow) {
                $eachRow['date_of_birth'] = (is_int($eachRow['date_of_birth'])
                    ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($eachRow['date_of_birth'])->format('Y-m-d')
                    : Carbon::createFromFormat('d/m/Y', $eachRow['date_of_birth']))->format('Y-m-d');
                $eachRow['date_uploaded'] = (is_int($eachRow['date_uploaded'])
                    ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($eachRow['date_uploaded'])->format('Y-m-d')
                    : Carbon::createFromFormat('d/m/Y', $eachRow['date_uploaded']))->format('Y-m-d');;

                $eachRow['date_processed_by_dwp'] =  (is_int($eachRow['date_processed_by_dwp'])
                    ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($eachRow['date_processed_by_dwp'])->format('Y-m-d')
                    : Carbon::createFromFormat('d/m/Y', $eachRow['date_processed_by_dwp']))->format('Y-m-d');
                return $eachRow;
            })->filter(function ($row) {
                return filled($row['postcode']) && filled($row['date_of_birth']);
            });
            DB::transaction(function () use ($rows) {
                $rows->each(function ($eachLead) {
                    try {
                        $lead = Lead::query()
                            ->with('leadCustomerAdditionalDetail')
                            ->where([
                                ['last_name', '=', $eachLead['surname']],
                                ['first_name', '=', $eachLead['forename']],
                                ['post_code', '=', strtoupper(removeSpace($eachLead['postcode']))],
                            ])->orWhere([
                                ['last_name', '=', $eachLead['forename']],
                                ['first_name', '=', $eachLead['surname']],
                                ['post_code', '=', strtoupper(removeSpace($eachLead['postcode']))],
                            ])->get();
                        if ($lead->count() > 1) {

                            //means multiple records found now need to query more for specific, so we find our row address in the coming leads
                            $response = $lead->filter(function ($item) use ($eachLead) {
                                return stripos($item?->plain_address, $eachLead['address_line_1']) !== false;
                            })?->first();
                            $result = $response?->leadCustomerAdditionalDetail?->update([
                                'datamatch_progress' => $eachLead['eco_4_verification_status'],
                                'urn' => $eachLead['urn'],
                                'data_match_sent_date' => $eachLead['date_uploaded'],
                                'date_processed_by_dwp' => $eachLead['date_processed_by_dwp'],

                            ]);
                            if ($result) {

                                Log::channel('data_match_result_file_read_log')->info('Data Match updated for ' . json_encode($response->toArray()) . ' against ' . json_encode($eachLead->toArray()));

                                $this->classResponse->totalUploadedRows++;
                            } else {
                                $this->classResponse->failedLeads[] = $eachLead->toArray();
                            }

                            // $response->leadAdditional()->update([]);
                        } elseif ($lead->count() == 1) {
                            $lead = $lead?->first();
                            $result = $lead?->leadCustomerAdditionalDetail?->update([
                                'datamatch_progress' => $eachLead['eco_4_verification_status'],
                                'urn' => $eachLead['urn'],
                                'data_match_sent_date' => $eachLead['date_uploaded'],
                                'date_processed_by_dwp' => $eachLead['date_processed_by_dwp'],
                            ]);
                            if ($result) {

                                Log::channel('data_match_result_file_read_log')->info('Data Match updated for ' . json_encode($lead->toArray()) . ' against ' . json_encode($eachLead->toArray()));
                                $this->classResponse->totalUploadedRows++;
                            } else {

                                $this->classResponse->failedLeads[] = $eachLead->toArray();
                            }

                            // $lead->leadAdditional()->update([]);

                            //exact one found just update it
                        } else {
                            //no found
                            Log::channel('data_match_result_file_read_log')->error('No match found for record' . json_encode($eachLead->ToArray()));
                            $this->classResponse->failedLeads[] = $eachLead->toArray();
                        }
                    } catch (Exception $e) {
                        dd($e->getMessage());
                        $this->classResponse->failedLeads[] = $eachLead->toArray();
                    }
                });
            });
        } catch (Exception $e) {
            $this->classResponse->failedLeads[] = $rows->toArray();
            $this->classResponse->status = 500;
            $this->classResponse->message = 'failed to upload, exception: ' . $e->getMessage();

            Log::channel('data_match_slack')->info('Error importing data match result:  ' . $e->getMessage());
            Log::channel('data_match_result_file_read_log')->info(
                'Error importing data match result:  ' . $e->getMessage()
            );
        }
    }
}
