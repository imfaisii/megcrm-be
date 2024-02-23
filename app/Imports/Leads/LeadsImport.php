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
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use function App\Helpers\formatCommas;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;



class LeadsImport implements ToCollection, WithHeadingRow, WithEvents
{

    public function __construct(public LeadResponseClass $classResponse)
    {

    }
    protected $class = GetAddress::class;
    public $name = 'LeadsImport';
    public function registerEvents(): array
    {
        return [
                // Handle by a closure.
            BeforeImport::class => function (BeforeImport $event) {
                $reader = $event->reader;
                $creator = $reader->getProperties()->getCreator();
                $totalRows = array_sum($reader->getTotalRows());
                $fileName = request()->file('file')->getClientOriginalName();
                // Log::driver('slack')->info("{$creator} has uploaded Leads file named as  {$fileName} with {$totalRows} rows.");
            },
        ];
    }

    public function collection(Collection $rows)
    {
        try {

            DB::beginTransaction();
            $arrayPostCodesAddresses = $rows->pluck('address', 'postcode')?->filter()?->map(function ($collection) {
                return formatCommas(trim($collection));
            })->toArray();
            $lead = Lead::pluck('address', 'post_code')->toArray();
            $addressToInclude = (array_diff_assoc($arrayPostCodesAddresses ?? [], $lead));

            $rows = $rows?->filter(function ($row) {
                return $row['address'] ?? false;
            })?->transform(function ($item, int $key) {
                return [
                    ...$item,
                    'address' => formatCommas(trim(data_get($item, 'address', '')))
                ];
            });
            $this->classResponse->alreadyFoundEnteries = $rows->whereNotIn('address', $addressToInclude)->all();
            foreach ($rows->whereIn('address', $addressToInclude) as $key => $row) {
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

                        if (filled($address)) {
                            // $address = formatCommas($address);
                            $address = resolve($this->class)->getCompleteAddress($address, $postCode, 'autocomplete') ?: $address;
                            $address = formatCommas(trim($address));
                        }

                        $name = $this->split_name($row['name'] ?? '');
                        $lead = Lead::firstOrCreate([
                            'post_code' => $postCode,
                            'address' => (string) $address,
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
                            'created_by_id' => auth()->id()
                        ]);
                        if ($lead->wasRecentlyCreated) {
                            $this->classResponse->totalUploadedRows += 1;
                        } else {
                            $this->classResponse->alreadyFoundEnteries[] = $row;
                        }
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
                    //Log::channel('lead_file_read_log')->info(
                    //"Error importing lead address else: " . $row['address']
                    //);
                }
            }
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
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
