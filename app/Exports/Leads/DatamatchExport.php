<?php

namespace App\Exports\Leads;

use App\Enums\DataMatch\DataMatchEnum;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use function App\Helpers\extractFirstNumericNumber;
use function App\Helpers\formatPostCodeWithSpace;
use function App\Helpers\getOnlyNumersFromString;
use function App\Helpers\removeStringFromString;
use function App\Helpers\replaceFirst;

class DatamatchExport implements FromCollection, WithHeadings, WithMapping, Responsable, WithStyles, WithEvents, WithColumnWidths, ShouldAutoSize
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

            3 => ['font' => ['bold' => true,]],




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
                'Postcode'
            ]
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
            $lead->first_name,
            $lead->last_name,
            Carbon::parse($lead->dob)->format('d/m/Y'),
            // $lead->sub_building ? extractFirstNumericNumber(getOnlyNumersFromString($lead->sub_building)) : ($lead->building_number ? extractFirstNumericNumber(getOnlyNumersFromString($lead->building_number)) : extractFirstNumericNumber(getOnlyNumersFromString($lead->address))),    // only fails when there is no number in sub building and buildingnumber  like flat one
            // $lead->sub_building ? removeStringFromString($lead->sub_building, $lead->address) : ($lead->building_number ? removeStringFromString($lead->building_number, $lead->address) : removeStringFromString(extractFirstNumericNumber(getOnlyNumersFromString($lead->address)), $lead->address)),
            $lead->sub_building ?: ($lead->building_number ?: extractFirstNumericNumber(getOnlyNumersFromString($lead->plain_address))),
            $lead->sub_building ? removeStringFromString($lead->sub_building, $lead->plain_address) : ($lead->building_number ? removeStringFromString($lead->building_number, $lead->plain_address) : removeStringFromString(extractFirstNumericNumber(getOnlyNumersFromString($lead->address)), $lead->plain_address)),
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
                    'data_match_sent_date' => now()
                ]);
            });
            Cache::store('file')->put('datamatch-download', $lead);
            return $lead;
        }




    }
    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
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
