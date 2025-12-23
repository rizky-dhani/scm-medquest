<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;
use App\Models\Location;
use App\Models\Room;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AllLocationsTemperatureHumidityExport implements WithMultipleSheets
{
    protected array $exportData;
    protected ?string $title = null;

    public function __construct(array $exportData, ?string $title = null)
    {
        $this->exportData = $exportData;
        $this->title = $title;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->exportData as $locationId => $data) {
            // Skip if no records
            if (!isset($data['records']) || $data['records']->isEmpty()) {
                continue;
            }

            $location = $data['location'] ?? Location::find($locationId);
            $locationName = 'Unknown Location';
            
            if ($location && !empty($location->location_name)) {
                $locationName = $location->location_name;
            } elseif (!empty($data['location_name'])) {
                $locationName = $data['location_name'];
            }
            
            // Use location name as sheet name, clean it for Excel
            $sheetName = preg_replace('/[^A-Za-z0-9_\- ]/', '', $locationName);
            $sheetName = substr($sheetName, 0, 31); // Excel sheet names max 31 characters
            
            // If sheet name is empty, create a default name
            if (empty($sheetName)) {
                $sheetName = 'Location_' . $locationId;
            }
            
            // Ensure unique sheet names
            $originalSheetName = $sheetName;
            $counter = 1;
            while (isset($sheets[$sheetName])) {
                $sheetName = substr($originalSheetName, 0, 27) . '_' . $counter;
                $counter++;
            }
            
            // Create a new sheet for this location using an anonymous class
            $sheets[$sheetName] = new class($data['records'], $locationName) implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping, WithEvents, WithTitle {
                protected Collection $records;
                protected ?string $title = null;

                public function __construct(Collection $records, ?string $title = null)
                {
                    $this->records = $records;
                    $this->title = $title;
                }

                public function title(): string
                {
                    return $this->title ?? 'Temperature Humidity Data';
                }

                public function collection()
                {
                    return $this->records;
                }

                public function registerEvents(): array
                {
                    return [
                        AfterSheet::class => function(AfterSheet $event) {
                            // Merge cells for time slot grouping in the new order
                            $sheet = $event->sheet->getDelegate();
                            
                            // Merge cells for each time slot group (in 02:00 to 23:00 order)
                            $sheet->mergeCells('E1:H1'); // 02:00
                            $sheet->mergeCells('I1:L1'); // 05:00
                            $sheet->mergeCells('M1:P1'); // 08:00
                            $sheet->mergeCells('Q1:T1'); // 11:00
                            $sheet->mergeCells('U1:X1'); // 14:00
                            $sheet->mergeCells('Y1:AB1'); // 17:00
                            $sheet->mergeCells('AC1:AF1'); // 20:00
                            $sheet->mergeCells('AG1:AK1'); // 23:00
                            
                            // Add labels for merged cells
                            $sheet->setCellValue('E1', '02:00');
                            $sheet->setCellValue('I1', '05:00');
                            $sheet->setCellValue('M1', '08:00');
                            $sheet->setCellValue('Q1', '11:00');
                            $sheet->setCellValue('U1', '14:00');
                            $sheet->setCellValue('Y1', '17:00');
                            $sheet->setCellValue('AC1', '20:00');
                            $sheet->setCellValue('AG1', '23:00');
                            
                            // Style the grouping row
                            $sheet->getStyle('A1:AK1')
                                ->getFont()
                                ->setBold(true);
                            $sheet->getStyle('A1:AK1')
                                ->getAlignment()
                                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        },
                    ];
                }

                public function styles(Worksheet $sheet)
                {
                    // Get the highest row and column
                    $highestRow = $sheet->getHighestRow();
                    $highestColumn = $sheet->getHighestColumn();
                    
                    // Apply center alignment to all cells
                    $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);

                    // Apply word wrap to the Room column
                    $sheet->getStyle("C1:C{$highestRow}")
                        ->getAlignment()
                        ->setWrapText(true);

                    // Apply borders to all cells
                    $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);

                    return [
                        // Header row (row 2)
                        2 => [
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ],
                    ];
                }

                public function headings(): array
                {
                    // Single row of detailed headers in 02:00 to 23:00 order
                    return [
                        'Date',
                        "Location",
                        "Room",
                        "SN",
                        'Time',      // 02:00
                        'Temp (°C)',
                        'RH (%)',
                        'PIC',
                        'Time',      // 05:00
                        'Temp (°C)',
                        'RH (%)',
                        'PIC',
                        'Time',      // 08:00
                        'Temp (°C)',
                        'RH (%)',
                        'PIC',
                        'Time',      // 11:00
                        'Temp (°C)',
                        'RH (%)',
                        'PIC',
                        'Time',      // 14:00
                        'Temp (°C)',
                        'RH (%)',
                        'PIC',
                        'Time',      // 17:00
                        'Temp (°C)',
                        'RH (%)',
                        'PIC',
                        'Time',      // 20:00
                        'Temp (°C)',
                        'RH (%)',
                        'PIC',
                        'Time',      // 23:00
                        'Temp (°C)',
                        'RH (%)',
                        'PIC',
                        'Reviewed By',
                        'Acknowledged By',
                        'Status',
                    ];
                }

                public function map($record): array
                {
                    // Collect all deviation times
                    $deviationTimes = [];
                    
                    // Check each time slot for deviations
                    $timeSlots = [
                        '02:00' => $record->temp_0200,
                        '05:00' => $record->temp_0500,
                        '08:00' => $record->temp_0800,
                        '11:00' => $record->temp_1100,
                        '14:00' => $record->temp_1400,
                        '17:00' => $record->temp_1700,
                        '20:00' => $record->temp_2000,
                        '23:00' => $record->temp_2300,
                    ];
                    
                    $minTemp = $record->roomTemperature->temperature_start;
                    $maxTemp = $record->roomTemperature->temperature_end;
                    
                    foreach ($timeSlots as $time => $temp) {
                        if ($temp !== null && ($temp < $minTemp || $temp > $maxTemp)) {
                            // Check if there's a deviation record for this time slot
                            $startTime = substr($time, 0, 5) . ':00';
                            $endTime = substr($time, 0, 5) . ':59';
                            
                            $deviation = $record->temperatureDeviations()
                                ->whereTime('time', '>=', $startTime)
                                ->whereTime('time', '<=', $endTime)
                                ->first();
                                
                            if ($deviation) {
                                $deviationTimes[] = $time;
                            }
                        }
                    }
                    
                    // Determine status
                    $status = empty($deviationTimes) ? 'Sesuai' : 'Menyimpang (' . implode(', ', $deviationTimes) . ')';

                    return [
                        Carbon::parse($record->date)->format('d/m/Y'),  // Formatted date string
                        $record->location->location_name,
                        $record->room->room_name,
                        $record->serialNumber->serial_number,
                        $record->time_0200 ? Carbon::parse($record->time_0200)->format('H:i') : 'N/A',  // 02:00
                        $record->temp_0200 !== null ? $record->temp_0200 . ' °C' : 'N/A',
                        $record->rh_0200 !== null ? $record->rh_0200 . '%' : 'N/A',
                        $record->pic_0200 ?? 'N/A',
                        $record->time_0500 ? Carbon::parse($record->time_0500)->format('H:i') : 'N/A',  // 05:00
                        $record->temp_0500 !== null ? $record->temp_0500 . ' °C' : 'N/A',
                        $record->rh_0500 !== null ? $record->rh_0500 . '%' : 'N/A',
                        $record->pic_0500 ?? 'N/A',
                        $record->time_0800 ? Carbon::parse($record->time_0800)->format('H:i') : 'N/A',  // 08:00
                        $record->temp_0800 !== null ? $record->temp_0800 . ' °C' : 'N/A',
                        $record->rh_0800 !== null ? $record->rh_0800 . '%' : 'N/A',
                        $record->pic_0800 ?? 'N/A',
                        $record->time_1100 ? Carbon::parse($record->time_1100)->format('H:i') : 'N/A',  // 11:00
                        $record->temp_1100 !== null ? $record->temp_1100 . ' °C' : 'N/A',
                        $record->rh_1100 !== null ? $record->rh_1100 . '%' : 'N/A',
                        $record->pic_1100 ?? 'N/A',
                        $record->time_1400 ? Carbon::parse($record->time_1400)->format('H:i') : 'N/A',  // 14:00
                        $record->temp_1400 !== null ? $record->temp_1400 . ' °C' : 'N/A',
                        $record->rh_1400 !== null ? $record->rh_1400 . '%' : 'N/A',
                        $record->pic_1400 ?? 'N/A',
                        $record->time_1700 ? Carbon::parse($record->time_1700)->format('H:i') : 'N/A',  // 17:00
                        $record->temp_1700 !== null ? $record->temp_1700 . ' °C' : 'N/A',
                        $record->rh_1700 !== null ? $record->rh_1700 . '%' : 'N/A',
                        $record->pic_1700 ?? 'N/A',
                        $record->time_2000 ? Carbon::parse($record->time_2000)->format('H:i') : 'N/A',  // 20:00
                        $record->temp_2000 !== null ? $record->temp_2000 . ' °C' : 'N/A',
                        $record->rh_2000 !== null ? $record->rh_2000 . '%' : 'N/A',
                        $record->pic_2000 ?? 'N/A',
                        $record->time_2300 ? Carbon::parse($record->time_2300)->format('H:i') : 'N/A',  // 23:00
                        $record->temp_2300 !== null ? $record->temp_2300 . ' °C' : 'N/A',
                        $record->rh_2300 !== null ? $record->rh_2300 . '%' : 'N/A',
                        $record->pic_2300 ?? 'N/A',
                        $record->reviewed_by ?? 'N/A',
                        $record->acknowledged_by ?? 'N/A',
                        $status,
                    ];
                }
            };
        }

        return $sheets;
    }
}