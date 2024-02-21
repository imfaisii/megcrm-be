<?php

namespace App\Exports\Leads;

use App\Models\Lead;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;

class DatamatchExport implements FromCollection, WithHeadings, WithMapping, Responsable
{
    use Exportable;

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     */
    private $fileName = "datamatch-required.xlsx";

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

    public function headings(): array
    {
        return [
            'First Name',
            'Middle Name',
            'Last Name',
            'Date of birth',
            'Post Code',
            'Address'
        ];
    }

    /**
     * @param Lead $lead
     */
    public function map($lead): array
    {
        return [
            $lead->first_name,
            $lead->middle_name,
            $lead->last_name,
            $lead->dob,
            $lead->post_code,
            $lead->address,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Lead::whereHas('leadCustomerAdditionalDetail', function ($query) {
            $query->where('is_datamatch_required', true);
        })->get();
    }
}
