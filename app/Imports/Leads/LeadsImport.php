<?php

namespace App\Imports\Leads;

use App\Classes\GetAddress;
use App\Classes\LeadResponseClass;

ini_set('memory_limit', '-1');

use App\Models\BenefitType;
use App\Models\Lead;
use App\Models\LeadGenerator;
use App\Models\LeadStatus;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class LeadsImport implements ToCollection, WithHeadingRow, ShouldQueue
{
    public function __construct(public LeadResponseClass $classResponse)
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
                        $leadGenerator = LeadGenerator::firstOrCreate(
                            [
                                'name' => $row['website'] ?? 'Lead Generator Default'
                            ],
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
                                'name' => $benefit
                            ])->id;
                        }


                        [$postCode, $address, $plainAddress, $city, $county, $country] = $apiClass->adressionApi($postCode ?? '', $address);

                        $name = $this->split_name($row['name'] ?? '');
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
                            'country' => $country
                        ]);

                        // Set Status
                        if (array_key_exists('status', $row->toArray())) {
                            $status = LeadStatus::firstOrCreate([
                                'name' => $row['status']
                            ], [
                                'color' => 'warning',
                                'created_by_id' => auth()->id()
                            ]);

                            $lead->setStatus($status->name, Arr::get($row, 'comments', 'Created via file upload, no comments found in file.'));
                        } else {
                            $lead->setStatus(LeadStatus::first()->name, 'Created via file upload');
                        }

                        // creating additional empty record for lead
                        $lead->leadCustomerAdditionalDetail()->create();

                        $lead->benefits()->syncWithPivotValues($benefitTypes, [
                            'created_by_id' => auth()->id()
                        ]);
                    } catch (Exception $exception) {
                        Log::channel('lead_file_read_log')->info(
                            "Error importing lead address: " . $address . ". " . $exception->getMessage()
                        );
                    }
                }
            }
        } catch (Exception $exception) {
            Log::channel('lead_file_read_log')->info(
                "Error importing lead address:: " . $row['address'] . ' message:: ' . $exception->getMessage()
            );

            $this->classResponse->failedLeads[] = $row['address'];
        }
    }

    public function split_name($name)
    {
        $parts = array();

        while (strlen(trim($name)) > 0) {
            $name = trim($name);
            $string = preg_replace('#.*\s([\w-]*)$#', '$1', $name);
            $parts[] = $string;
            $name = trim(preg_replace('#' . preg_quote($string, '#') . '#', '', $name));
        }

        if (empty($parts)) {
            return false;
        }

        $parts = array_reverse($parts);
        $name = array();
        $name['first_name'] = $parts[0];
        $name['middle_name'] = (isset($parts[2])) ? $parts[1] : '';
        $name['last_name'] = (isset($parts[2])) ? $parts[2] : (isset($parts[1]) ? $parts[1] : '');

        return $name;
    }
}
