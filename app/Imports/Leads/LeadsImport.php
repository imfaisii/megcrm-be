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
                $firstName = null;
                $lastName = null;
                $benefitTypes = [];

                try {
                    // lead generator
                    $leadGenerator = LeadGenerator::firstOrCreate(
                        [
                            'mask_name' => $row['website'] ?? 'Lead Generator Default'
                        ],
                        ['name' => "Lead Generator " . LeadGenerator::count() + 1]
                    );

                    // name
                    if (Str::contains(Arr::get($row, 'name', ''), " ")) {
                        $name = explode(" ", $row['name']);

                        if (count($name) > 1) {
                            $firstName = $name[0];
                            $lastName = filled($name[1]) ? $name[1]  : null;
                        }
                    } else {
                        $firstName = $row['name'] ?? null;
                    }

                    $email = Arr::get($row, 'email', null);
                    $phoneNo = Arr::get($row, 'contact_number', null);
                    $dob = Arr::get($row, 'dob', null);
                    $postCode = Arr::get($row, 'postcode', null);
                    $address = Arr::get($row, 'address', null);
                    $benefits = Arr::get($row, 'benefits', []);
                    $benefits = explode("\n", $benefits);

                    foreach ($benefits as $key => $benefit) {
                        $benefitTypes[] = BenefitType::firstOrCreate([
                            'name' => $benefit
                        ])->id;
                    }

                    $lead = Lead::firstOrCreate([
                        'address' => $address
                    ], [
                        'title' => 'Mr',
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $email,
                        'phone_no' => $phoneNo,
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
            }
        }
    }
}
