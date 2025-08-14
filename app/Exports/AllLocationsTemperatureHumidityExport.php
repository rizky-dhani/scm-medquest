<?php

namespace App\Exports;

use App\Models\Location;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AllLocationsTemperatureHumidityExport implements WithMultipleSheets
{
    protected array $exportData;

    public function __construct(array $exportData)
    {
        $this->exportData = $exportData;
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
            
            $sheets[$sheetName] = new TemperatureHumidityExport($data['records'], $locationName);
        }

        return $sheets;
    }
}