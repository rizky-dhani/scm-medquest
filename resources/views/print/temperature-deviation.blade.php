<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temperature Deviation Export PDF</title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            margin: 0;
            padding: 0;
        }

        .table {
            font-size: 11px;
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .table th,
        .table td {
            padding: 0.3rem;
            vertical-align: middle;
            border: 1px solid black !important;
            word-wrap: break-word;
        }

        thead {
            display: table-header-group;
        }

        .room-section {
            page-break-after: always;
            position: relative;
        }

        .room-section:last-child {
            page-break-after: auto;
        }

        .header-container {
            border: 1px solid black;
            margin-bottom: 5px;
        }

        .footer-section {
            page-break-inside: avoid;
            margin-top: 15px;
        }

        .border-black {
            border: 1px solid black !important;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }
        
        .col-no { width: 35px; }
        .col-date { width: 85px; }
        .col-time { width: 45px; }
        .col-temp { width: 80px; }
        .col-length { width: 80px; }
        .col-pic { width: 65px; }
    </style>
</head>

<body>
    @php
// Ensure we have grouped deviations even if the route didn't provide them as expected
$renderGroups =
    isset($groupedDeviations) && $groupedDeviations->count() > 0
    ? $groupedDeviations
    : (isset($temperatureDeviations)
        ? $temperatureDeviations->groupBy('room_id')
        : collect());
    @endphp

    @forelse($renderGroups as $roomId => $roomDeviations)
            <div class="room-section">
                {{-- Header Section --}}
                @php
        $firstDeviation = $roomDeviations->first();
                @endphp
                <div class="container-fluid mb-2">
                    <div class="row header-container">
                        <div class="col-2 d-flex justify-content-center align-items-center px-0"
                            style="background-color: #0E0F97 !important;">
                            <img src="{{ asset('assets/images/LOGO-MEDQUEST-HD.png') }}" alt=""
                                style="width: 100%;">
                        </div>
                        <div class="col-7 d-flex flex-column">
                            <div class="row align-items-center h-50 border-bottom border-start border-end border-black">
                                <h6 class="fw-bolder text-uppercase text-center mb-0" style="width: 100%;">Form</h6>
                            </div>
                            <div class="row align-items-center h-50 border-start border-end border-black">
                                <h6 class="fw-bolder text-uppercase text-center mb-0" style="width: 100%;">Monitoring
                                    Temperature Deviations</h6>
                            </div>
                        </div>
                        <div class="col-3 ps-1">
                            <div class="row">
                                <div class="col-5">
                                    <p class="mb-0" style="font-size: 11px;">Doc. No</p>
                                    <p class="mb-0" style="font-size: 11px;">Effective Date</p>
                                    <p class="mb-0" style="font-size: 11px;">Revision No.</p>
                                    <p class="mb-0" style="font-size: 11px;">Page No.</p>
                                </div>
                                <div class="col-7">
                                    <p class="mb-0" style="font-size: 11px;">:&ensp;MJG-FOR-SCM.02.002-01</p>
                                    <p class="mb-0" style="font-size: 11px;">:&ensp;11 JUN 2023</p>
                                    <p class="mb-0" style="font-size: 11px;">:&ensp;08</p>
                                    <p class="mb-0" style="font-size: 11px;">:&ensp;@pageNumber of @totalPages</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-1">
                        <div class="col-12 border border-black py-1">
                            <p class="mb-0 fw-bold text-center" style="font-size: 14px">Annexure of the Temperature and
                                Humidity Monitoring Form (<i>Lampiran Formulir Pemantauan Suhu dan Kelembapan</i>)</p>
                        </div>
                    </div>

                    <div class="row mb-1">
                        <div class="col-6 border border-black border-end-0 py-1">
                            <div class="row">
                                <div class="col-5">
                                    <p class="mb-0" style="font-size: 12px;">Period (<i>Periode</i>)</p>
                                    <p class="mb-0" style="font-size: 12px;">Location (<i>Lokasi</i>) / Serial No.</p>
                                    <p class="mb-0" style="font-size: 12px;">Storage temp standard * <br> (<i>Standar suhu
                                            penyimpanan</i>)</p>
                                </div>
                                <div class="col-7">
                                    <p class="mb-0 text-uppercase" style="font-size: 12px;">
                                        :&ensp;{{ \Carbon\Carbon::parse($firstDeviation->date)->format('M Y') }}</p>
                                    <p class="mb-0" style="font-size: 12px;">
                                        :&ensp;{{ ($firstDeviation->room->room_name ?? '-') . ' ' . ($firstDeviation->location->location_name ?? '-') }}
                                        / {{ $firstDeviation->serialNumber->serial_number ?? '-' }}</p>
                                    <p class="mb-0" style="font-size: 12px;">
                                        :&ensp;{{ $firstDeviation->roomTemperature->temperature_start ?? '-' }}°C to
                                        {{ $firstDeviation->roomTemperature->temperature_end ?? '-' }}°C</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 border border-black py-1">
                            <p class="mb-0" style="font-size: 11px;">*) Give a mark (&#10003;) in the appropriate box
                                (<i>Beri tanda (&#10003;) pada kotak yang sesuai</i>)
                            </p>
                            <p class="mb-0" style="font-size: 11px;">***) Cross out inappropriate text (<i>Coret tulisan
                                    yang tidak sesuai</i>)</p>
                            <p class="mb-0" style="font-size: 11px;">****) Signature and initial name (<i>Tanda tangan dan
                                    inisial nama</i>) </p>
                            <p class="mb-0" style="font-size: 11px;">*****) Signature initial name and date (<i>Tanda
                                    tangan, inisial nama dan tanggal</i>) </p>

                        </div>
                    </div>

                    <div class="row mb-1">
                        <div class="col-12 border border-black py-1">
                            <div class="row">
                                <div class="col-3">
                                    <p class="mb-0" style="font-size: 12px;">Reason of deviations **) <br> (<i>Alasan
                                            penyimpangan **</i>)</p>
                                </div>
                                <div class="col-9">
                                    <p class="mb-0" style="font-size: 11px;">:&ensp; A.&nbsp;There were many people in the
                                        room, so that the room temperature rised (<i>Banyak orang didalam ruangan, sehingga
                                            suhu ruangan meningkat</i>)</p>
                                    <p class="mb-0" style="font-size: 11px;">&ensp;&ensp;B.&nbsp;There were activities
                                        related to packaging / moving goods (<i>Sedang ada aktivitas terkait dengan
                                            pengemasan / pemindahan barang</i>)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-1">
                        <div class="col-12 border border-black py-1 bg-light">
                            <p class="mb-0 fw-bold text-center text-uppercase" style="font-size: 14px">Jika Terjadi
                                Penyimpangan Suhu Laporkan Segera ke Quality Assurance (QA)</p>
                        </div>
                    </div>
                </div>

                {{-- Table Section --}}
                <div class="container-fluid px-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-light">
                                <th class="text-center align-middle" style="width: 30px">No</th>
                                <th class="text-center" style="font-size: 11px">Date<br>(<i>Tanggal</i>)</th>
                                <th class="text-center" style="font-size: 11px">Time<br>(<i>Jam</i>)</th>
                                <th class="text-center" style="font-size: 11px">Temperature deviation<br>(<i>Penyimpangan
                                        suhu</i>)<br>(°C)</th>
                                <th class="text-center" style="font-size: 11px">Length of temperature
                                    deviation<br>(<i>Lamanya penyimpangan suhu</i>)<br>(Menit) ***</th>
                                <th class="text-center" style="font-size: 11px">Reason of the deviations **<br>(<i>Alasan
                                        penyimpangan</i>) **</th>
                                <th class="text-center" style="font-size: 11px">PIC ****<br>(<i>SCM</i>)</th>
                                <th class="text-center" style="font-size: 11px">Risk Analysis of impact
                                    deviation<br>(<i>Analisis risiko dari dampak penyimpangan</i>)</th>
                                <th class="text-center" style="font-size: 11px">Analyzed by ****<br>(<i>QA</i>)</th>
                            </tr>
                        </thead>
                                            <tbody>
                                                @foreach ($roomDeviations as $record)
                                                    <tr>
                                                        <td class="text-center">{{ $loop->iteration }}</td>
                                                        <td class="text-center">
                                                            {{ strtoupper(\Carbon\Carbon::parse($record->date)->format('d M Y')) }}</td>
                                                        <td class="text-center">{{ \Carbon\Carbon::parse($record->time)->format('H:i') }}</td>
                                                        <td class="text-center">{{ $record->temperature_deviation ?? '-' }}°C</td>
                                                        <td class="text-center">{{ $record->length_temperature_deviation ?? '-' }}</td>
                                                        <td class="text-center">{{ $record->deviation_reason ?? '-' }}</td>
                                                        <td class="text-center">{{ $record->pic ?? '-' }}</td>
                                                        <td class="text-center">{{ $record->risk_analysis ?? '-' }}</td>
                                                        <td class="text-center">{{ $record->analyzer_pic ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                        
                                                @php
                                                    $currentRowCount = count($roomDeviations);
                                                    $minRows = 10;
                                                @endphp
                        
                                                @for ($i = $currentRowCount + 1; $i <= $minRows; $i++)
                                                    <tr>
                                                        <td class="text-center">{{ $i }}</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                @endfor
                                            </tbody>                    </table>
                </div>
                <div class="container-fluid footer-section">
                    <div class="row">
                        <div class="col-4 border border-black py-2 d-flex align-items-center">
                            <p class="fw-bolder text-center mb-0" style="font-size: 10px;">Especially for Grifols Diagnostics' products, report to QAS
                                department and Grifols Diagnostics' for any temperature excursion immediately <br><br>
                                (Khusus produk Grifols Diagnostics, segera laporkan ke departemen QAS dan prinsipal Grifols
                                Diagnostics jika terjadi penyimpangan suhu)</p>
                        </div>
                        <div class="col-8 border border-start-0 border-black">
                        </div>
                    </div>
                </div>
            </div>
    @empty
        <div class="container mt-5">
            <div class="alert alert-warning text-center">
                No temperature deviation records found for the selected criteria.
            </div>
        </div>
    @endforelse
</body>

</html>
