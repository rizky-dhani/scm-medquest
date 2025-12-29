<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;
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

class TemperatureDeviationExport implements WithMultipleSheets
{
    protected Collection $records;
    protected ?string $title = null;
    protected Collection $groupedRecords;

    public function __construct(Collection $records, ?string $title = null)
    {
        $this->records = $records;
        $this->title = $title;
        
        // Filter records to ensure they're all from the same location
        // This addresses the user's concern about seeing multiple locations
        $locationIds = $records->pluck('location_id')->unique();
        
        if ($locationIds->count() > 1) {
            // If we have records from multiple locations, filter to only the first location
            $firstLocationId = $locationIds->first();
            $records = $records->filter(function ($record) use ($firstLocationId) {
                return $record->location_id == $firstLocationId;
            });
        }
        
        // Group records by room
        $this->groupedRecords = $records->groupBy(function ($item) {
            return $item->room_id;
        });
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        
        foreach ($this->groupedRecords as $roomId => $records) {
            // Get room name for sheet title
            $room = Room::find($roomId);
            $roomName = $room ? $room->room_name : 'Unknown Room';
            
            // Clean room name for Excel sheet name (max 31 characters)
            $sheetName = preg_replace('/[^A-Za-z0-9_\- ]/', '', $roomName);
            $sheetName = substr($sheetName, 0, 31);
            
            // If sheet name is empty, create a default name
            if (empty($sheetName)) {
                $sheetName = 'Room_' . $roomId;
            }
            
            // Ensure unique sheet names
            $originalSheetName = $sheetName;
            $counter = 1;
            while (isset($sheets[$sheetName])) {
                $sheetName = substr($originalSheetName, 0, 27) . '_' . $counter;
                $counter++;
            }
            
            // Create a new sheet for this room using an anonymous class
            $sheets[$sheetName] = new class($records, $roomName) implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping, WithEvents, WithTitle {
                protected Collection $records;
                protected ?string $title = null;
                protected int $rowIndex = 0;

                public function __construct(Collection $records, ?string $title = null)
                {
                    $this->records = $records;
                    $this->title = $title;
                }

                public function title(): string
                {
                    return $this->title ?? 'Temperature Deviation Data';
                }

                public function collection()
                {
                    return $this->records;
                }

                public function registerEvents(): array
                {
                    return [
                        AfterSheet::class => function(AfterSheet $event) {
                            $sheet = $event->sheet->getDelegate();
                            
                            // Style the header row
                            $sheet->getStyle('A1:I1')
                                ->getFont()
                                ->setBold(true);
                            $sheet->getStyle('A1:I1')
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

                    // Apply word wrap to all cells to support line breaks in headings
                    $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                        ->getAlignment()
                        ->setWrapText(true);

                    // Apply borders to all cells
                    $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);

                    return [
                        // Header row
                        1 => [
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ],
                    ];
                }

                public function headings(): array
                {
                    return [
                        'Number',
                        "Date\n(Tanggal)",
                        "Time\n(Jam)",
                        "Temperature Deviation\n(Penyimpangan Suhu)\n(°C)",
                        "Length of temperature deviation\n(Lamanya penyimpangan suhu)\n(Menit)",
                        "Reason of the deviations **\n(Alasan penyimpangan) **",
                        "P.I.C ****\n(SCM)",
                        "Risk analysis of impact deviation\n(Analisa risiko dari dampak penyimpangan)",
                        "Analyzed by ****\n(QA)",
                    ];
                }
                
                public function map($record): array
                {
                    $this->rowIndex++;
                    return [
                        $this->rowIndex,
                        strtoupper(Carbon::parse($record->date)->format('d M Y')),
                        Carbon::parse($record->time)->format('H:i'),
                        $record->temperature_deviation ?? '-',
                        $record->length_temperature_deviation ?? '-',
                        $record->deviation_reason ?? '-',
                        $record->pic ?? '-',
                        $record->risk_analysis ?? '-',
                        $record->analyzer_pic ?? '-',
                    ];
                }
            };
        }
        
        return $sheets;
    }
}
