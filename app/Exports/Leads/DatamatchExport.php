<?php

namespace App\Exports\Leads;

use App\Enums\DataMatch\DataMatchEnum;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use function App\Helpers\formatPostCodeWithSpace;
use function App\Helpers\removeStringFromString;
use function App\Helpers\removetillFirstNuermicSpcae;

class DatamatchExport implements FromCollection, Responsable, ShouldAutoSize, WithColumnWidths, WithEvents, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     */
    private $fileName = 'datamatch-required.xlsx';

    /**
     * Optional Writer Type
     */
    private $writerType = Excel::XLSX;

    /**
     * Optional headers
     */
    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 15,
            'C' => 30,
            'D' => 30,
            'E' => 12,
            'F' => 30,
            // 'G' => 30,
            // 'H' => 30,
            // 'I' => 30,
            'J' => 30,
            'K' => 30,
            'L' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => false, 'size' => 22]],

            3 => ['font' => ['bold' => true]],

        ];
    }

    public function startCell(): string
    {
        return 'B2';
    }

    public function headings(): array
    {
        return [
            ['EST DWP Datamatch Import Template'],
            [],
            [
                '',
                'Service User ID',
                'Surname',
                'Forename',
                'Date Of Birth',
                'Property Name or Number',
                'Address Line 1',
                'Address Line 2',
                'Address Line 3',
                'Town',
                'County',
                'Postcode',
            ],
        ];
    }

    /**
     * @param  Lead  $lead
     */
    public function map($lead): array
    {
        return [
            '', // for empty column
            '', // surname
            $lead->last_name,
            $lead->first_name,
            Carbon::parse($lead->dob)->format('d/m/Y'),
            /* the beneath line first check if the sub building then that else building_number else buildingname else fir plain address s exact first number
            // $lead->sub_building ?: ($lead->building_number ?: (array_key_exists('buildingname', $lead->raw_api_response) ? $lead->raw_api_response['buildingname'] : extractFirstNumericNumber(getOnlyNumersFromString($lead->plain_address)))),
            // $lead->sub_building ? removeStringFromString($lead->sub_building, $lead->plain_address) : ($lead->building_number ? removeStringFromString($lead->building_number, $lead->plain_address) : (array_key_exists('buildingname', $lead->raw_api_response) ? removeStringFromString($lead->raw_api_response['buildingname'], $lead->plain_address) : removeStringFromString(extractFirstNumericNumber(getOnlyNumersFromString($lead->plain_address)), $lead->plain_address))),
            */
            $lead->sub_building ?: ($lead->building_number ?: (array_key_exists('buildingname', $lead->raw_api_response ?? []) ? $lead->raw_api_response['buildingname'] : removetillFirstNuermicSpcae($lead->plain_address))),
            $lead->sub_building ? removeStringFromString($lead->sub_building, $lead->plain_address) : ($lead->building_number ? removeStringFromString($lead->building_number, $lead->plain_address) : (array_key_exists('buildingname', $lead->raw_api_response ?? []) ? removeStringFromString($lead->raw_api_response['buildingname'], $lead->plain_address) : removeStringFromString(removetillFirstNuermicSpcae($lead->plain_address), $lead->plain_address))),
            '',
            '',
            $lead->city,
            $lead->country,
            $lead->actual_post_code ?? formatPostCodeWithSpace($lead->post_code),
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // if there is no new data match required then download the old result
        $query = Lead::withWhereHas('leadCustomerAdditionalDetail', function ($query) {
            $query->where('is_datamatch_required', true);
        })->get();
        if (blank($query) && Cache::store('file')->has('datamatch-download')) {
            return Cache::store('file')->get('datamatch-download');
        } else {
            $lead = Lead::withWhereHas('leadCustomerAdditionalDetail', function ($query) {
                $query->where('is_datamatch_required', true);
            })->get()->each(function ($lead) {
                $lead->leadCustomerAdditionalDetail->update([
                    'datamatch_progress' => DataMatchEnum::StatusSent,
                    'is_datamatch_required' => false,
                    'data_match_sent_date' => now(),
                ]);
            });
            Cache::store('file')->put('datamatch-download', $lead);

            return $lead;
        }

    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                Log::channel('slack-crm')->info('the user '.auth()->user()->email.' has downloaded the file ');
                // Style the first row as bold text.
                // $event->sheet->getStyle('A1')->getFont()->setBold(true);

                // Styling a specific cell by coordinate.
                // $event->sheet->getStyle('A1')->getFont()->setSize(16);
                // $sheet = $event->sheet->getDelegate();

                // // Shift cells from row 3 one cell ahead.
                // $sheet->shiftRows(3, 1, 1);

                // Lead::withWhereHas('leadCustomerAdditionalDetail', function ($query) {
                //     $query->where('is_datamatch_required', true);
                // })->get()->each(function ($lead) {
                //     $lead->leadCustomerAdditionalDetail->update([
                //         'datamatch_progress' => DataMatchEnum::StatusSent,
                //         'is_datamatch_required' => false,
                //         'data_match_sent_date' => now()
                //     ]);
                // });

                // Styling the third row with a light gray background.
                $event->sheet->getStyle('B3:L3')->getFill()->setFillType(Fill::FILL_SOLID);
                $event->sheet->getStyle('B3:L3')->getFill()->getStartColor()->setARGB('FFDDDDDD');
            },
        ];
    }
}
