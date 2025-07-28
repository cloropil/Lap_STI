<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Harian</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 14px;
            padding: 40px;
            line-height: 1.6;
        }

        .kop-surat {
            position: relative;
            display: flex;
            align-items: center;
            border-bottom: 3px double black;
            padding-bottom: 10px;
            margin-bottom: 20px;
            height: 100px;
        }

        .kop-surat img {
            width: 80px;
            height: auto;
            margin-right: 15px;
            margin-left: 10px;
        }

        .kop-text {
            position: absolute;
            left: 0;
            right: 0;
            text-align: center;
            margin: auto;
            width: fit-content;
        }

        .kop-text h1 {
            font-size: 18px;
            margin: 0;
        }

        .kop-text h2 {
            font-size: 16px;
            margin: 0;
            text-transform: uppercase;
        }

        .kop-text p {
            font-size: 12px;
            margin: 2px 0;
        }

        h3 {
            text-align: center;
            margin-top: 20px;
            text-transform: uppercase;
            font-size: 16px;
        }

        .section {
            margin-top: 20px;
        }

        .section table {
            width: 100%;
            border-collapse: collapse;
        }

        .section td {
            vertical-align: top;
            padding: 2px 4px;
        }

        .label {
            width: 160px;
        }

        .colon {
            width: 10px;
        }

        .images {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .images img {
            height: 100px;
            border: 1px solid #aaa;
            border-radius: 4px;
        }

        .stopclock-table {
            margin-top: 10px;
            border-collapse: collapse;
            width: 100%;
            font-size: 13px;
        }

        .stopclock-table th, .stopclock-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
        }

        .stopclock-table th {
            background-color: #f2f2f2;
        }

        @media print {
            @page {
                margin: 20mm;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="kop-surat">
        <img src="{{ asset('logo-pln.png') }}" alt="Logo PLN">
        <div class="kop-text">
            <h1>PT PLN (Persero)</h1>
            <h2>UNIT INDUK WILAYAH SUMATERA BARAT</h2>
            <p>Jl. Dr. Wahidin No.8 Padang, Sumbar 25171 - Indonesia</p>
            <p>Telepon: (0751) 33447 | Email: pln123@pln.co.id</p>
        </div>
    </div>

    <h3>Laporan Harian Jaringan Office & SCADA<br>
    Tanggal: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</h3>

    <div class="section">
        <p><strong>A. Laporan Harian (08:00 - 16:00 WIB)</strong></p>
        <p>I. Gangguan Jaringan SCADA : <strong>Nihil</strong></p>
        <p>II. Layanan WAN Office : <strong>Nihil</strong></p>
        <p>III. Tiket Keluhan : <strong>{{ $tikets->count() }}</strong></p>
    </div>

    @foreach ($tikets as $index => $tiket)
    <div class="section">
        <p><strong>{{ $index + 1 }}. [UIW SUMBAR] {{ $tiket->lokasi->lokasi }}</strong></p>
        <table>
            <tr><td class="label">SID</td><td class="colon">:</td><td>{{ $tiket->lokasi->sid }}</td></tr>
            <tr><td class="label">No Tiket</td><td class="colon">:</td><td>{{ $tiket->no_tiket }}</td></tr>
            <tr><td class="label">Open Tiket</td><td class="colon">:</td><td>{{ \Carbon\Carbon::parse($tiket->open_tiket)->format('d-m-Y (H:i \W\I\B)') }}</td></tr>
            <tr><td class="label">Stopclock</td><td class="colon">:</td><td>{{ $tiket->stopclock ?? '-' }}</td></tr>
            <tr><td class="label">Link Up</td><td class="colon">:</td><td>{{ $tiket->link_up ? \Carbon\Carbon::parse($tiket->link_up)->format('d-m-Y (H:i \W\I\B)') : '--:-- WIB' }}</td></tr>
            <tr><td class="label">Durasi</td><td class="colon">:</td><td>{{ $tiket->durasi ?? '> 3 Jam' }}</td></tr>
            <tr><td class="label">Durasi</td><td class="colon">:</td><td>{{ $tiket->durasi ?? '> 3 Jam' }}</td></tr>
            <tr><td class="label">Jenis Gangguan</td><td class="colon">:</td><td>{{ $tiket->jenis_gangguan ?? '-' }}</td></tr>
            <tr><td class="label">Penyebab</td><td class="colon">:</td><td>{{ $tiket->penyebab }}</td></tr>
            <tr><td class="label">Action</td><td class="colon">:</td><td>{!! nl2br(e($tiket->action ?? '⌛')) !!}</td></tr>
            <tr><td class="label">Status Koneksi</td><td class="colon">:</td><td>{{ $tiket->status_koneksi ?? '⌛' }}</td></tr>
            <tr><td class="label">Status Tiket</td><td class="colon">:</td><td>{{ $tiket->status_tiket ?? '-' }}</td></tr>
        </table>

        @if ($tiket->action_images)
        <div class="images">
            @foreach (json_decode($tiket->action_images, true) as $img)
                <img src="{{ asset('storage/' . $img) }}" alt="Gambar" />
            @endforeach
        </div>
        @endif

        @if ($tiket->stopclocks && $tiket->stopclocks->count())
        @php $totalMenit = 0; @endphp
        <table class="stopclock-table">
            <thead>
                <tr>
                    <th>no</th>
                    <th>Start Clock</th>
                    <th>Stop Clock</th>
                    <th>Alasan</th>
                    <!-- <th>Durasi</th> -->
                </tr>
            </thead>
            <tbody>
                @foreach ($tiket->stopclocks as $i => $sc)
                    @php
                        $durasiMenit = max(0, \Carbon\Carbon::parse($sc->stop_clock)->diffInMinutes(\Carbon\Carbon::parse($sc->start_clock)));
                        $totalMenit += $durasiMenit;
                        $jam = floor($durasiMenit / 60);
                        $menit = $durasiMenit % 60;
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($sc->start_clock)->format('d-m-Y H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($sc->stop_clock)->format('d-m-Y H:i') }}</td>
                        <td>{{ $sc->alasan }}</td>
                        <!-- <td>{{ $jam > 0 ? $jam . ' Jam ' : '' }}{{ $menit }} Menit</td> -->
                    </tr>
                @endforeach
                @php
                    $totalJam = floor($totalMenit / 60);
                    $totalSisaMenit = $totalMenit % 60;
                @endphp
                <tr>
                    <!-- <td colspan="4" style="text-align: right;"><strong>Total Stopclock</strong></td> -->
                    <!-- <td><strong>{{ $totalJam > 0 ? $totalJam . ' Jam ' : '' }}{{ $totalSisaMenit }} Menit</strong></td> -->
                </tr>
            </tbody>
        </table>
        @endif
    </div>
    @endforeach

    <br><br>
    <div style="width: 100%; display: flex; justify-content: flex-end;">
        <div style="text-align: center;">
            <p>Padang, {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</p>
            <!-- <p><strong>UNIT INDUK WILAYAH SUMATERA BARAT</strong></p> -->
            <br><br><br>
            <p><strong><u>Nama Petugas</u></strong><br>NIP. 123456789</p>
        </div>
    </div>
</body>
</html>
