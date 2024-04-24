<?php

namespace App\Exports\Leads;

use App\Enums\AppEnum;
use App\Enums\DataMatch\DataMatchEnum;
use App\Models\Lead;
use Carbon\Carbon;
use DateTime;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use function App\Helpers\formatPostCodeWithSpace;
use function App\Helpers\generateARandomNumberNotInGivenArray;
use function App\Helpers\removeStringFromString;
use function App\Helpers\removetillFirstNuermicSpcae;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DatamatchExport implements FromCollection, Responsable, ShouldAutoSize, WithColumnWidths, WithEvents, WithHeadings, WithMapping, WithStyles, WithColumnFormatting
{

    public function __construct(public string $destinationFile)
    {
    }
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
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
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
                '',
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
            $lead->id,
            $lead->last_name,
            $lead->first_name,
            Str::before(Date::PHPToExcel(DateTime::createFromFormat('d/m/Y', Carbon::parse($lead->dob)->format('d/m/Y'))->getTimestamp()), '.'),

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
            ''
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // leads with seond recipient must be included twice as it becomes another lead

        $leads = Lead::withWhereHas('leadCustomerAdditionalDetail', function ($query) {
            $query->where('is_datamatch_required', true);
        })->with('secondReceipent')->get()->each(function ($lead) {
            $lead->leadCustomerAdditionalDetail->update([
                'datamatch_progress' => DataMatchEnum::StatusSent,
                'is_datamatch_required' => false,
                'data_match_sent_date' => now(),
            ]);
        });
        $secondReceipetsLeads = $leads?->whereNotNull('secondReceipent')?->map(function ($lead) use ($leads) {
            $clonedLead = unserialize(serialize($lead)); // Deep copy of the lead object
            $clonedLead->id = generateARandomNumberNotInGivenArray($leads?->pluck('id')->all());
            $clonedLead->first_name = $lead?->secondReceipent?->first_name;
            $clonedLead->last_name = $lead?->secondReceipent?->last_name;
            $clonedLead->dob = $lead?->secondReceipent?->dob;

            return $clonedLead;
        });
        $mergedLeads = $leads?->merge($secondReceipetsLeads);

        return filled($mergedLeads) ? $mergedLeads : collect([]);

    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                if (app()->isProduction()) {
                    Log::channel('data_match_slack')->info('the user ' . auth()->user()->email . ' has downloaded the file ');

                }

                $event->sheet->setTitle('EST DWP Datamatch');
                Storage::copy(AppEnum::TEMPLATE_PATH, $this->destinationFile);
                $spreadsheet = IOFactory::load(storage_path("app/$this->destinationFile"));

                // Select the specific sheet where you want to write the data
                $worksheet = $spreadsheet->getActiveSheet();

                // Get the data from the Maatwebsite export class
                $data = $this->collection();

                // Write the data to the selected sheet starting from row 4
                $row = 4;
                foreach ($data as $lead) {
                    $worksheet->setCellValue('B' . $row, $lead->id);
                    $worksheet->setCellValue('C' . $row, $lead->last_name);
                    $worksheet->setCellValue('D' . $row, $lead->first_name);
                    $worksheet->setCellValue('E' . $row, Carbon::parse($lead->dob)->format('d/m/Y'));
                    $worksheet->setCellValue('F' . $row, $lead->sub_building ?: ($lead->building_number ?: (array_key_exists('buildingname', $lead->raw_api_response ?? []) ? $lead->raw_api_response['buildingname'] : removetillFirstNuermicSpcae($lead->plain_address))));
                    $worksheet->setCellValue('G' . $row, $lead->sub_building ? removeStringFromString($lead->sub_building, $lead->plain_address) : ($lead->building_number ? removeStringFromString($lead->building_number, $lead->plain_address) : (array_key_exists('buildingname', $lead->raw_api_response ?? []) ? removeStringFromString($lead->raw_api_response['buildingname'], $lead->plain_address) : removeStringFromString(removetillFirstNuermicSpcae($lead->plain_address), $lead->plain_address))));
                    $worksheet->setCellValue('H' . $row, '');
                    $worksheet->setCellValue('I' . $row, '');
                    $worksheet->setCellValue('J' . $row, $lead->city);
                    $worksheet->setCellValue('K' . $row, $lead->country);
                    $worksheet->setCellValue('L' . $row, $lead->actual_post_code ?? formatPostCodeWithSpace($lead->post_code));
                    $row++;
                }

                // Save the changes to the existing file
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save(storage_path( $this->destinationFile));


                // Styling the third row with a light gray background.
                $event->sheet->getStyle('B3:L3')->getFill()->setFillType(Fill::FILL_SOLID);
                $event->sheet->getStyle('B3:L3')->getFill()->getStartColor()->setARGB('FFDDDDDD');
            },
        ];
    }
}
