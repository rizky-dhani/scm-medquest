<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header Template for Temperature Deviation Export PDF</title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
</head>
<body>
    <div class="container-fluid">
        <div class="row mb-1 border border-black" id="header">
            <div class="col-2 px-0">
                <img src="{{ asset('assets/images/LOGO-MEDQUEST-HD.png') }}" alt="" style="width: 100%;">
            </div>
            <div class="col-7 d-flex flex-column border-black">
                <div class="row align-items-center h-50 border-bottom border-start border-end border-black">
                    <h5 class="fw-bolder text-uppercase text-center mb-0">Form</h5>
                </div>
                <div class="row align-items-center h-50 border-start border-end border-black">
                    <h5 class="fw-bolder text-uppercase text-center mb-0">Monitoring Temperature Deviations</h5>
                </div>
            </div>
            <div class="col-3 ps-1">
                <div class="row">
                    <div class="col-3">
                        <p class="mb-0" style="font-size: 13px; padding-left: 2%;">Doc. No</p>
                        <p class="mb-0" style="font-size: 13px; padding-left: 2%;">Effective Date</p>
                        <p class="mb-0" style="font-size: 13px; padding-left: 2%;">Revision No.</p>
                        <p class="mb-0" style="font-size: 13px; padding-left: 2%;">Page No.</p>
                    </div>
                    <div class="col-9">
                        <p class="mb-0" style="font-size: 13px; padding-left: 2%;">:&emsp;</p>
                        <p class="mb-0" style="font-size: 13px; padding-left: 2%;">:&emsp;</p>
                        <p class="mb-0" style="font-size: 13px; padding-left: 2%;">:&emsp;</p>
                        <p class="mb-0" style="font-size: 13px; padding-left: 2%;">:&emsp;</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-1" id="title">
            <div class="col-12 border border-black">
                <p class="mb-0 fw-bold ps-1 text-center" style="font-size: 16px">Annexure of the Temperature and Humidity Monitoring Form (<i>Lampiran Formulir Pemantauan Suhu dan Kelembapan</i>)</p>
            </div>
        </div>
        <div class="row mb-1" id="detail">
            <div class="col-7 pe-0 border border-black">
                <div class="row">
                    <div class="col-3">
                        <p class="mb-0" style="font-size: 16px; padding-left: 1% !important;">Period (<i>Periode</i>)</p>
                        <p class="mb-0" style="font-size: 16px; padding-left: 1% !important;">Location (<i>Lokasi</i>) / Serial No.</p>
                        <p class="mb-0" style="font-size: 16px; padding-left: 1% !important;">Storage temperature standard <br> (<i>Standar suhu penyimpanan</i>)</p>
                    </div>
                    <div class="col-9">
                        <p class="mb-0" style="font-size: 16px; padding-left: 1% !important;">:&emsp;</p>
                        <p class="mb-0" style="font-size: 16px; padding-left: 1% !important;">:&emsp;</p>
                        <p class="mb-0" style="font-size: 16px; padding-left: 1% !important;">:&emsp;</p>
                    </div>
                </div>
            </div>
            <div class="col-5 pe-0 border border-black">
                <p class="mb-0" style="font-size: 16px;">*) Give a mark (&#10003;) in the appropriate box (<i>Beri tanda (&#10003;) pada kotak yang sesuai</i>)</p>
                <p class="mb-0" style="font-size: 16px;">***) Cross out inappropriate text (<i>Coret tulisan yang tidak sesuai</i>)</p>
                <p class="mb-0" style="font-size: 16px;">****) Signature and initial name (<i>Tanda tangan dan inisial nama</i>) </p>
                <p class="mb-0" style="font-size: 16px;">*****) Signature initial name and date (<i>Tanda tangan, inisial nama dan tanggal</i>) </p>

            </div>
        </div>
        <div class="row mb-1" id="detail">
            <div class="col-12 pe-0 border border-black">
                <div class="row">
                    <div class="col-2" style="width: 14.2%">
                        <p class="mb-0" style="font-size: 16px; padding-left: 1% !important;">Reason of the deviations **) <br> (<i>Alasan penyimpangan **</i>)</p>
                    </div>
                    <div class="col-10" style="width: 85.8%">
                        <p class="mb-0" style="font-size: 16px; padding-left: 1% !important;">:&emsp; A.&nbsp;There were many people in the room, so that the room temperature rised (<i>Banyak orang didalam ruangan, sehingga suhu ruangan meningkat</i>)</p>
                        <p class="mb-0" style="font-size: 16px; padding-left: 1% !important;">&ensp;&emsp;B.&nbsp;There were activities related to packaging / moving goods (<i>Sedang ada aktivitas terkait dengan pengemasan / pemindahan barang</i>)</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-1" id="title">
            <div class="col-12 border border-black">
                <p class="mb-0 fw-bold ps-1 text-center text-uppercase" style="font-size: 16px">Jika Terjadi Penyimpangan Suhu Laporkan Segera ke Quality Assurance (QA)</p>
            </div>
        </div>
    </div>
</body>
</html>