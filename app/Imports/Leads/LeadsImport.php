<?php

namespace App\Imports\Leads;

use App\Classes\GetAddress;
use App\Classes\LeadResponseClass;

ini_set('memory_limit', '-1');

use App\Models\BenefitType;
use App\Models\Lead;
use App\Models\LeadGenerator;
use App\Models\LeadStatus;
use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use function App\Helpers\formatCommas;
use function App\Helpers\removeSpace;
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
            /* this code get addresses against postcodes from the file and in one format like postcode without space and lower case , address without comma space and lowercas */
            $rows = $rows?->filter(function ($row) {   // removes empty addresses row from the collection
                return $row['address'] ?? false;
            });
            $arrayPostCodesAddresses = $rows->transform(function ($row) {
                return [
                    ...$row,
                    'address' => strtolower(formatCommas(data_get($row, 'address', ''))),
                    'postcode' => strtolower(removeSpace(preg_replace('/[^a-zA-Z0-9\s]/', ' ', data_get($row, 'postcode', '')))),

                ];
            })
                ->groupBy('postcode')
                ->map(function ($items) {
                    return $items->pluck('address')->toArray();
                })
                ->toArray();
            /*  get addresses against postcodes as the above format */
            $leads = Lead::get(['post_code', 'address'])->transform(function ($value) {
                return [
                    'post_code' =>  strtolower(removeSpace(preg_replace('/[^a-zA-Z0-9\s]/', ' ',$value->post_code))),
                    'address' => strtolower(formatCommas($value->address)),
                ];
            })->groupBy('post_code')->transform(function ($value) {
                return $value->pluck('address')->toArray();
            })->toArray();
            $diffKeys = Arr::map($arrayPostCodesAddresses, function ($val, $key) use ($leads) {
                return array_filter($val, function ($value) use ($leads, $key) {
                    return !in_array($value, Arr::get($leads, $key, []));

                });
            });
            $allowedPostCodes = array_keys($diffKeys);
            $allwoedAddresses = Arr::flatten($diffKeys);   // these are the addresses to include in this shift with that post codes


            $this->classResponse->alreadyFoundEnteries = $rows->whereNotIn('address', $allwoedAddresses)->whereNotIn('postcode', $allowedPostCodes)->all();
            foreach ($rows->whereIn('address', $allwoedAddresses)->whereIn('postcode', $allowedPostCodes) as $key => $row) {
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
                        $oldAddress = $address;
                        if (filled($address)) {
                            // $address = formatCommas($address);
                            $address = resolve($this->class)->getCompleteAddress($address, $postCode, 'autocomplete') ?: $address;
                            $address = strtolower(formatCommas($address)); // its necessary because it has to compare that with db

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
                            'file_address' => $oldAddress,
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
                            $this->classResponse->alreadyFoundEnteries[] = [...$row, 'isDataBase' => true, 'id' => $lead->id];
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
