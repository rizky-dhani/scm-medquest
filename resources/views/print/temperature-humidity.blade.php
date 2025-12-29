<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temperature Humidity Export PDF</title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .table {
            font-size: 12px;
        }
        .table th, .table td {
            padding: 0.25rem;
            vertical-align: middle;
        }
        .location-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .date,.time,.temp,.rh,.pic{
            font-size: 10px;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="container-fluid mb-1">
        <div class="row mb-1 border border-black" id="header">
            <div class="col-2 d-flex justify-content-center align-items-center px-0" style="background-color: #0E0F97 !important;">
                <img src="{{ asset('assets/images/LOGO-MEDQUEST-HD.png') }}" alt="" style="width: 100%;">
            </div>
            <div class="col-7 d-flex flex-column border-black">
                <div class="row align-items-center h-50 border-bottom border-start border-end border-black">
                    <h5 class="fw-bolder text-uppercase text-center mb-0">Form</h5>
                </div>
                <div class="row align-items-center h-50 border-start border-end border-black">
                    <h5 class="fw-bolder text-uppercase text-center mb-0">Temperature And Humidity Monitoring</h5>
                </div>
            </div>
            <div class="col-3 ps-1">
                <div class="row">
                    <div class="col-5">
                        <p class="mb-0" style="font-size: 12px; padding-left: 2%;">Doc. No</p>
                        <p class="mb-0" style="font-size: 12px; padding-left: 2%;">Effective Date</p>
                        <p class="mb-0" style="font-size: 12px; padding-left: 2%;">Revision No.</p>
                        <p class="mb-0" style="font-size: 12px; padding-left: 2%;">Page No.</p>
                    </div>
                    <div class="col-7">
                        <p class="mb-0" style="font-size: 12px; padding-left: 2%;">:&ensp;MJG-FOR-SCM.02.002-01</p>
                        <p class="mb-0" style="font-size: 12px; padding-left: 2%;">:&ensp;12 JUN 2023</p>
                        <p class="mb-0" style="font-size: 12px; padding-left: 2%;">:&ensp;09</p>
                        <p class="mb-0" style="font-size: 12px; padding-left: 2%;">:&ensp; @pageNumber of @totalPages</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-1" id="temperature-range">
            <div class="col-7 pe-0 border border-black border-end-0">
                <p class="mb-0 fw-bold ps-1" style="font-size: 14px; padding-left: 2%;">Acceptable Temperature Range:</p>
                <div class="row">
                    <div class="col-6">
                        <p class="mb-0" style="font-size: 14px; padding-left: 1% !important;">a.&ensp;Ambient storage
                            temperature (<i>suhu kamar</i>)</p>
                        <p class="mb-0" style="font-size: 14px; padding-left: 1% !important;">b.&ensp;Cold storage
                            temperature (<i>suhu dingin</i>)</p>
                        <p class="mb-0" style="font-size: 14px; padding-left: 1% !important;">b.&ensp;Frozen storage
                            temperature (<i>suhu beku</i>)</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-0" style="font-size: 14px; padding-left: 1% !important;">:&emsp;15°C to 30°C or 15°C to
                            25°C</p>
                        <p class="mb-0" style="font-size: 14px; padding-left: 1% !important;">:&emsp;2°C to 8°C</p>
                        <p class="mb-0" style="font-size: 14px; padding-left: 1% !important;">:&emsp;-35°C to -15°C or -25°C
                            to -10°C</p>
                    </div>
                </div>
            </div>
            <div class="col-5 pe-0 border border-black">
                <p class="mb-0 fw-bold ps-1" style="font-size: 14px; padding-left: 2%;">Acceptable Humidity Range:</p>
                <div class="row">
                    <div class="col-4">
                        <p class="mb-0" style="font-size: 14px; padding-left: 1% !important;">d.&ensp;Humidity (RH)</p>
                    </div>
                    <div class="col-8">
                        <p class="mb-0" style="font-size: 14px; padding-left: 1% !important;">:&emsp;30% to 90%</p>
                    </div>
                </div>
                <p class="mb-0" style="font-size: 14px;">Note:&emsp;<b>Humidity for Freeze and Refrigerator are not
                        monitored.</b> so it can be filled with "N/A" (Not Applicable)</p>
            </div>
        </div>
        <div class="row" id="detail">
            <div class="col-7 pe-0 border border-black border-end-0">
                <div class="row">
                    <div class="col-3">
                        <p class="mb-0" style="font-size: 14px; padding-left: 1% !important;">Period</p>
                        <p class="mb-0" style="font-size: 14px; padding-left: 1% !important;">Location / Serial No.</p>
                        <p class="mb-0" style="font-size: 14px; padding-left: 1% !important;">Observed Temperature</p>
                    </div>
                    <div class="col-9">
                        <p class="mb-0 text-uppercase" style="font-size: 14px; padding-left: 1% !important;">:&emsp;{{ \Carbon\Carbon::parse($tempHumidity->first()?->period)->format('M Y') ?? '-' }}</p>
                        <p class="mb-0" style="font-size: 14px; padding-left: 1% !important;">:&emsp;{{ $tempHumidity->first()?->location?->location_name ?? '-' }} / {{ $tempHumidity->first()?->serialNumber?->serial_number ?? '-' }}</p>
                        <p class="mb-0" style="font-size: 14px; padding-left: 1% !important;">:&emsp;{{ $tempHumidity->first()?->roomTemperature?->temperature_start ?? '-' }}°C to {{ $tempHumidity->first()?->roomTemperature?->temperature_end ?? '-' }}°C</p>
                    </div>
                </div>
            </div>
            <div class="col-5 pe-0 border border-black">
                <p class="mb-0" style="font-size: 14px;">*) Give a mark (&#10003;) in the appropriate box</p>
                <p class="mb-0" style="font-size: 14px;">**) Initial name & date</p>
                <p class="mb-0" style="font-size: 14px;">***) Signature, initial name and date </p>

            </div>
        </div>
    </div>
    {{-- Report --}}
    <div class="container-fluid px-0">
        <table class="table table-bordered border border-black">
            {{-- Table Header --}}
            <thead>
                <tr>
                    <th rowspan="2" class="border border-black text-center align-middle" style="width: 40px">Date</th>
                    <th colspan="4" class="text-center">0200</th>
                    <th colspan="4" class="text-center">0500</th>
                    <th colspan="4" class="text-center">0800</th>
                    <th colspan="4" class="text-center">1100</th>
                    <th colspan="4" class="text-center">1400</th>
                    <th colspan="4" class="text-center">1700</th>
                    <th colspan="4" class="text-center">2000</th>
                    <th colspan="4" class="text-center">2300</th>
                </tr>
                <tr>
                    {{-- 0200 --}}
                    <th class="text-center time-column">Time</th>
                    <th class="text-center temp-column">Temp</th>
                    <th class="text-center rh-column">RH</th>
                    <th class="text-center pic-column">PIC</th>
                    {{-- 0500 --}}
                    <th class="text-center time-column">Time</th>
                    <th class="text-center temp-column">Temp</th>
                    <th class="text-center rh-column">RH</th>
                    <th class="text-center pic-column">PIC</th>
                    {{-- 0800 --}}
                    <th class="text-center time-column">Time</th>
                    <th class="text-center temp-column">Temp</th>
                    <th class="text-center rh-column">RH</th>
                    <th class="text-center pic-column">PIC</th>
                    {{-- 1100 --}}
                    <th class="text-center time-column">Time</th>
                    <th class="text-center temp-column">Temp</th>
                    <th class="text-center rh-column">RH</th>
                    <th class="text-center pic-column">PIC</th>
                    {{-- 1400 --}}
                    <th class="text-center time-column">Time</th>
                    <th class="text-center temp-column">Temp</th>
                    <th class="text-center rh-column">RH</th>
                    <th class="text-center pic-column">PIC</th>
                    {{-- 1700 --}}
                    <th class="text-center time-column">Time</th>
                    <th class="text-center temp-column">Temp</th>
                    <th class="text-center rh-column">RH</th>
                    <th class="text-center pic-column">PIC</th>
                    {{-- 2000 --}}
                    <th class="text-center time-column">Time</th>
                    <th class="text-center temp-column">Temp</th>
                    <th class="text-center rh-column">RH</th>
                    <th class="text-center pic-column">PIC</th>
                    {{-- 2300 --}}
                    <th class="text-center time-column">Time</th>
                    <th class="text-center temp-column">Temp</th>
                    <th class="text-center rh-column">RH</th>
                    <th class="text-center pic-column">PIC</th>
                </tr>
            </thead>
            <tbody>
                {{-- Data Rows --}}
                @forelse($tempHumidity as $record)
                    <tr>
                        <td class="text-center">{{ \Carbon\Carbon::parse($record->date)->format('d') }}</td>

                        {{-- 0200 --}}
                        <td class="text-center time">{{ $record->time_0200 ? \Carbon\Carbon::parse($record->time_0200)->format('H:i') : '-' }}</td>
                        <td class="text-center temp">{{ $record->temp_0200 ?? '-' }}</td>
                        <td class="text-center rh">{{ $record->rh_0200 ?? '-' }}</td>
                        <td class="text-center pic">{{ $record->formatPicSignature('pic_0200') }}</td>

                        {{-- 0500 --}}
                        <td class="text-center time">{{ $record->time_0500 ? \Carbon\Carbon::parse($record->time_0500)->format('H:i') : '-' }}</td>
                        <td class="text-center temp">{{ $record->temp_0500 ?? '-' }}</td>
                        <td class="text-center rh">{{ $record->rh_0500 ?? '-' }}</td>
                        <td class="text-center pic">{{ $record->formatPicSignature('pic_0500') }}</td>

                        {{-- 0800 --}}
                        <td class="text-center time">{{ $record->time_0800 ? \Carbon\Carbon::parse($record->time_0800)->format('H:i') : '-' }}</td>
                        <td class="text-center temp">{{ $record->temp_0800 ?? '-' }}</td>
                        <td class="text-center rh">{{ $record->rh_0800 ?? '-' }}</td>
                        <td class="text-center pic">{{ $record->formatPicSignature('pic_0800') }}</td>

                        {{-- 1100 --}}
                        <td class="text-center time">{{ $record->time_1100 ? \Carbon\Carbon::parse($record->time_1100)->format('H:i') : '-' }}</td>
                        <td class="text-center temp">{{ $record->temp_1100 ?? '-' }}</td>
                        <td class="text-center rh">{{ $record->rh_1100 ?? '-' }}</td>
                        <td class="text-center pic">{{ $record->formatPicSignature('pic_1100') }}</td>

                        {{-- 1400 --}}
                        <td class="text-center time">{{ $record->time_1400 ? \Carbon\Carbon::parse($record->time_1400)->format('H:i') : '-' }}</td>
                        <td class="text-center temp">{{ $record->temp_1400 ?? '-' }}</td>
                        <td class="text-center rh">{{ $record->rh_1400 ?? '-' }}</td>
                        <td class="text-center pic">{{ $record->formatPicSignature('pic_1400') }}</td>

                        {{-- 1700 --}}
                        <td class="text-center time">{{ $record->time_1700 ? \Carbon\Carbon::parse($record->time_1700)->format('H:i') : '-' }}</td>
                        <td class="text-center temp">{{ $record->temp_1700 ?? '-' }}</td>
                        <td class="text-center rh">{{ $record->rh_1700 ?? '-' }}</td>
                        <td class="text-center pic">{{ $record->formatPicSignature('pic_1700') }}</td>

                        {{-- 2000 --}}
                        <td class="text-center time">{{ $record->time_2000 ? \Carbon\Carbon::parse($record->time_2000)->format('H:i') : '-' }}</td>
                        <td class="text-center temp">{{ $record->temp_2000 ?? '-' }}</td>
                        <td class="text-center rh">{{ $record->rh_2000 ?? '-' }}</td>
                        <td class="text-center pic">{{ $record->formatPicSignature('pic_2000') }}</td>

                        {{-- 2300 --}}
                        <td class="text-center time">{{ $record->time_2300 ? \Carbon\Carbon::parse($record->time_2300)->format('H:i') : '-' }}</td>
                        <td class="text-center temp">{{ $record->temp_2300 ?? '-' }}</td>
                        <td class="text-center rh">{{ $record->rh_2300 ?? '-' }}</td>
                        <td class="text-center pic">{{ $record->formatPicSignature('pic_2300') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="33" class="text-center">No records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
