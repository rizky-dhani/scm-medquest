<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\TemperatureDeviation;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemperatureDeviationExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
{
    
    protected Collection $records;

    public function __construct(Collection $records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records;
    }

    public function styles(Worksheet $sheet)
    {
        // Center align entire sheet content
        $highestRow = $sheet->getHighestRow(); // get total number of rows

        // Apply center alignment to all cells from A1 to last column & row
        $sheet->getStyle("A1:{$sheet->getHighestColumn()}{$highestRow}")
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Bold and center only for header row
        return [
            1 => [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Location',
            'Date',
            'Temperature Deviation (°C)',
            'Length of Temperature Deviation (Menit/Jam)',
            'Reason of Deviation',
            'P.I.C (SCM)',
            'Risk Analysis of impact deviation',
            'Analyzed by (QA)',
            'Reviewed By',
            'Acknowledged By',
        ];
    }
    
    public function map($record): array
    {
        return [
            $record->location->location_name . ' / ' . $record->serialNumber->serial_number,
            Carbon::parse($record->date)->format('d'),
            $record->temperature_deviation. ' °C' ?? '-',
            $record->length_temperature_deviation ?? '-',
            $record->deviation_reason ?? '-',
            $record->pic ?? '-',
            $record->risk_analysis ?? '-',
            $record->analyzer_pic ?? '-',
            $record->reviewed_by ?? '-',
            $record->acknowledged_by ?? '-',
        ];
    }
}
