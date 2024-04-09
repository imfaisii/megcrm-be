<?php

namespace App\Imports\Leads;

use App\Classes\GetAddress;
use App\Classes\LeadResponseClass;
use App\Jobs\AircallContactCreationJob;

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 0);

use App\Models\BenefitType;
use App\Models\Lead;
use App\Models\LeadGenerator;
use App\Models\LeadStatus;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use function App\Helpers\split_name;

class LeadsImport implements ToCollection, WithHeadingRow
{
    public function __construct(public LeadResponseClass $classResponse, public $newlyCreatedLeads = [])
    {
        //
    }

    public function collection(Collection $rows)
    {
        try {
            $apiClass = new GetAddress();
            $this->classResponse->failedLeads = [];

            foreach ($rows as $key => $row) {

                if (isset($row['address']) && $row['address'] !== null) {
                    $benefitTypes = [];

                    try {
                        // lead generator
                        $leadGeneratorName = $row['website'] ?? 'Lead Generator Default';
                        $leadGenerator = LeadGenerator::firstOrCreate(
                            [
                                'name' => $leadGeneratorName,
                            ],
                            ['sender_id' => substr($leadGeneratorName, 0, 11)],
                        );
                        $email = Arr::get($row, 'email', null);
                        $phoneNo = Arr::get($row, 'contact_number', '000000');
                        $dob = Arr::get($row, 'dob', null);
                        $postCode = Arr::get($row, 'postcode', '00000');
                        $address = Arr::get($row, 'address', null);
                        $benefits = Arr::get($row, 'benefits', []);
                        $benefits = explode("\n", $benefits);

                        foreach ($benefits as $key => $benefit) {
                            $benefitTypes[] = BenefitType::firstOrCreate([
                                'name' => $benefit,
                            ])->id;
                        }

                        [$postCode, $address, $plainAddress, $city, $county, $country, $buildingNumber, $subBuilding, $RawApiResponse, $actualPostCode] = $apiClass->adressionApi($postCode ?? '', $address);
                        $name = split_name($row['name'] ?? '');
                        $lead = Lead::firstOrCreate([
                            'post_code' => $postCode,
                            'address' => $address,
                        ], [
                            'title' => 'Mr',
                            'first_name' => $name['first_name'] ?? '',
                            'middle_name' => $name['middle_name'] ?? '',
                            'last_name' => $name['last_name'] ?? '',
                            'email' => $email,
                            'dob' => is_null($dob)
                                ? now()->format('Y-m-d') : (is_int($dob)
                                    ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dob)->format('Y-m-d')
                                    : $dob),
                            'phone_no' => $phoneNo ?? '00000',
                            'lead_generator_id' => $leadGenerator->id,
                            'user_id' => auth()->id(),
                            'created_by_id' => auth()->id(),
                            'plain_address' => $plainAddress,
                            'county' => $county,
                            'city' => $city,
                            'country' => $country,
                            'building_number' => $buildingNumber,
                            'sub_building' => $subBuilding,
                            'raw_api_response' => $RawApiResponse,
                            'actual_post_code' => $actualPostCode,
                        ]);
                        // check if its new created add it to an for later sending for creating contact on air call
                        if ($lead->wasRecentlyCreated) {
                            $this->newlyCreatedLeads[] = $lead->toArray();
                            
                            // creating additional empty record for lead
                            $lead->leadCustomerAdditionalDetail()->create();
                        }

                        // Set Status
                        if (array_key_exists('status', $row->toArray())) {
                            $status = LeadStatus::firstOrCreate([
                                'name' => $row['status'],
                            ], [
                                'color' => 'warning',
                                'created_by_id' => auth()->id(),
                            ]);

                            $lead->setStatus($status->name, Arr::get($row, 'comments', 'Created via file upload, no comments found in file.'));
                        } else {
                            $lead->setStatus(LeadStatus::first()->name, 'Created via file upload');
                        }

                        $lead->benefits()->syncWithPivotValues($benefitTypes, [
                            'created_by_id' => auth()->id(),
                        ]);
                    } catch (Exception $exception) {
                        Log::channel('lead_file_read_log')->info(
                            'Error importing lead address: '.$row['address'].'. '.$exception->getMessage()
                        );
                    }
                }
            }
        } catch (Exception $exception) {
            Log::channel('lead_file_read_log')->info(
                'Exception importing lead address:: '.$row['address'].' message:: '.$exception->getMessage()
            );

            $this->classResponse->failedLeads[] = $row['address'];
        }

        try {

            $chunks = array_chunk($this->newlyCreatedLeads, 30);
            foreach ($chunks as $key => $eachchunk) {
                AircallContactCreationJob::dispatch($eachchunk)->delay($key != 0 ? 120 : null);
            }
        } catch (Exception $exception) {
        }
    }
}
