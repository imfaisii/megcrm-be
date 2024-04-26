<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CreateFailedLeadsExport implements FromCollection,  ShouldAutoSize, WithColumnWidths, WithHeadings, WithStyles
{



    public function __construct(public array $failedLeads)
    {
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->failedLeads);
    }


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
            'M' => 10,
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
            ['EST DWP Datamatch Verification Download'],
            [],
            [
                '',
                'Service User ID',
                'Landlord Surname',
                'Landlord Forename',
                'Surname',
                'Forename',
                'Date of Birth',
                'Property Name or Number',
                'Address Line 1',
                'Address Line 2',
                'Address Line 3',
                'Town',
                'County',
                'Postcode',
                'URN',
                'ECO 3 Verification Status',
                'ECO 4 Verification Status',
                'Owner Status',
                'Date Uploaded',
                'Date Processed by DWP',
            ],
        ];
    }


}
