<?php

namespace App\Exports\Leads;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DatamatchExport implements FromCollection, WithHeadings, WithMapping
{
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
