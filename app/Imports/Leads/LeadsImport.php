<?php

namespace App\Imports\Leads;

use App\Models\BenefitType;
use App\Models\Lead;
use App\Models\LeadGenerator;
use App\Models\LeadStatus;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LeadsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {
            if (isset($row['address']) && $row['address'] !== null) {
                $benefitTypes = [];

                try {
                    // lead generator
                    $leadGenerator = LeadGenerator::firstOrCreate(
                        [
                            'mask_name' => $row['website'] ?? 'Lead Generator Default'
                        ],
                        ['name' => "Lead Generator " . LeadGenerator::count() + 1]
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

                    $name = $this->split_name($row['name'] ?? '');

                    $lead = Lead::firstOrCreate([
                        'address' => (string)$address,
                        'phone' => $phoneNo,
                        'email' => $email
                    ], [
                        'title' => 'Mr',
                        'first_name' => $name['first_name'] ?? '',
                        'middle_name' => $name['middle_name'] ?? '',
                        'last_name' => $name['last_name'] ?? '',
                        'email' => $email,
                        'phone_no' => $phoneNo ?? '00000',
                        'dob' => is_null($dob)
                            ? now()->format('Y-m-d') : (is_int($dob)
                                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dob)->format('Y-m-d')
                                : $dob),
                        'post_code' => $postCode,
                        'lead_generator_id' => $leadGenerator->id,
                        'user_id' => auth()->id(),
                        'created_by_id' => auth()->id()
                    ]);

                    $lead->setStatus(LeadStatus::first()->name, 'Created via file upload');

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
            } else {
                Log::channel('lead_file_read_log')->info(
                    "Error importing lead address else: " . $row['address']
                );
            }
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
