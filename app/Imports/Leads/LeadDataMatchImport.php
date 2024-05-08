<?php

namespace App\Imports\Leads;

use App\Classes\LeadResponseClass;
use App\Models\Lead;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

use function App\Helpers\meg_decrypts;
use function App\Helpers\removeSpace;

class LeadDataMatchImport extends DefaultValueBinder implements ToCollection, WithHeadingRow
{

    public array $leadsUpdatedIds = [];
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
                : Carbon::createFromFormat('d/m/Y', $eachRow['date_uploaded']))->format('Y-m-d');
                ;

                $eachRow['date_processed_by_dwp'] = (is_int($eachRow['date_processed_by_dwp'])
                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($eachRow['date_processed_by_dwp'])->format('Y-m-d')
                : Carbon::createFromFormat('d/m/Y', $eachRow['date_processed_by_dwp']))->format('Y-m-d');

                $eachRow['service_user_id'] = filled($eachRow['service_user_id']) ? meg_decrypts($eachRow['service_user_id']) : null;
                return $eachRow;
            })->filter(function ($row) {
                return filled($row['postcode']) && filled($row['date_of_birth']);
            });
            DB::transaction(function () use ($rows) {
                $rows->each(function ($eachLead) {
                    try {
                        $lead = $this->getAssociatedLeadModel($eachLead->toArray());
                        if (blank($lead)) {
                            //no associated Lead Found .. just log and add it to failed leads
                            Log::channel('data_match_result_file_read_log')->error('No match found for record' . json_encode($eachLead->ToArray()));
                            $this->classResponse->failedLeads[] = $eachLead->toArray();
                            return true;  // skip that iteration

                        }
                        if ($lead->count() > 1) {

                            //means multiple records found now need to query more for specific, so we find our row address in the coming leads
                            $response = $lead->filter(function ($item) use ($eachLead) {
                                return stripos($item?->plain_address, $eachLead['address_line_1']) !== false;
                            })?->first();
                            /* if that lead is already updated in this upload and that result was matched then don't do anything */
                            if (in_array($response?->id, $this->leadsUpdatedIds) && $response->leadCustomerAdditionalDetail->datamatch_progress == 'Matched') {
                                array_push($this->leadsUpdatedIds, $response->id);
                                Log::channel('data_match_result_file_read_log')->info('Data Match updated for ' . json_encode($response->toArray()) . ' against ' . json_encode($eachLead->toArray()));
                                $this->classResponse->totalUploadedRows++;
                                return true;  // skip that entry
                            }

                            $result = $response?->leadCustomerAdditionalDetail?->update([
                                'datamatch_progress' => $eachLead['eco_4_verification_status'],
                                'urn' => $eachLead['urn'],
                                'data_match_sent_date' => $eachLead['date_uploaded'],
                                'datamatch_progress_date' => $eachLead['date_processed_by_dwp'],
                                'result_first_name' => $eachLead['forename'],
                                'result_last_name' => $eachLead['surname'],

                            ]);
                            if ($result) {
                                array_push($this->leadsUpdatedIds, $response->id);
                                Log::channel('data_match_result_file_read_log')->info('Data Match updated for ' . json_encode($response->toArray()) . ' against ' . json_encode($eachLead->toArray()));
                                $this->classResponse->totalUploadedRows++;
                            } else {
                                $this->classResponse->failedLeads[] = $eachLead->toArray();
                            }

                            // $response->leadAdditional()->update([]);
                        } elseif ($lead->count() == 1) {
                            $lead = $lead?->first();

                            if (in_array($lead?->id, $this->leadsUpdatedIds) && $lead->leadCustomerAdditionalDetail->datamatch_progress == 'Matched') {
                                array_push($this->leadsUpdatedIds, $lead->id);

                                Log::channel('data_match_result_file_read_log')->info('Data Match updated for ' . json_encode($lead->toArray()) . ' against ' . json_encode($eachLead->toArray()));
                                $this->classResponse->totalUploadedRows++;
                                return true;
                            }

                            $result = $lead?->leadCustomerAdditionalDetail?->update([
                                'datamatch_progress' => $eachLead['eco_4_verification_status'],
                                'urn' => $eachLead['urn'],
                                'data_match_sent_date' => $eachLead['date_uploaded'],
                                'datamatch_progress_date' => $eachLead['date_processed_by_dwp'],
                                'result_first_name' => $eachLead['forename'],
                                'result_last_name' => $eachLead['surname'],

                            ]);
                            if ($result) {
                                array_push($this->leadsUpdatedIds, $lead->id);

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


    private function getAssociatedLeadModel(array $eachLead): ?EloquentCollection
    {
        try {
            if ($eachLead['service_user_id'] ?? false) {
                return Lead::query()->with('leadCustomerAdditionalDetail')->Where('id', $eachLead['service_user_id'])->get();
            } else {
                return Lead::query()
                    ->where(function ($q) use ($eachLead) {
                        $q->where(function ($query) use ($eachLead) {
                            return $query
                                ->where([
                                    ['last_name', '=', $eachLead['surname']],
                                    ['first_name', '=', $eachLead['forename']],
                                    ['post_code', '=', strtoupper(removeSpace($eachLead['postcode']))],
                                ])->orWhere([
                                        ['last_name', '=', $eachLead['forename']],
                                        ['first_name', '=', $eachLead['surname']],
                                        ['post_code', '=', strtoupper(removeSpace($eachLead['postcode']))],
                                    ])->orWhere([
                                        ['post_code', '=', strtoupper(removeSpace($eachLead['postcode']))],
                                        [
                                            'dob',
                                            '=',
                                            $eachLead['date_of_birth'],
                                        ],
                                    ]);
                        })->orWhereHas('secondReceipent', function ($query) use ($eachLead) {
                            $query->where([
                                ['last_name', '=', $eachLead['surname']],
                                ['first_name', '=', $eachLead['forename']],
                                [
                                    'dob',
                                    '=',
                                    $eachLead['date_of_birth'],
                                ],
                            ])->orWhere([
                                        ['last_name', '=', $eachLead['forename']],
                                        ['first_name', '=', $eachLead['surname']],
                                        [
                                            'dob',
                                            '=',
                                            $eachLead['date_of_birth'],
                                        ],
                                    ]);
                        });
                    })
                    ->with('leadCustomerAdditionalDetail')->get();
            }
        } catch (Exception $e) {
            return null;
        }
    }
}
