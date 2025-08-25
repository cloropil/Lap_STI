@extends('layouts.app')

@section('title', 'Laporan Harian')

@section('content')
@php
    // Pisahkan tiket berdasarkan jenis gangguan
    $scadaTikets = $tikets->where('jenis_gangguan', 'SCADA');
    $wanTikets = $tikets->where('jenis_gangguan', 'WAN Office');
    $keluhanTikets = $tikets->where('jenis_gangguan', 'Keluhan');

    $scadaCount = $scadaTikets->count();
    $wanCount = $wanTikets->count();
    $keluhanCount = $keluhanTikets->count();
@endphp

<div class="container mx-auto px-4 pt-10">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Laporan Harian Jaringan Office & SCADA</h2>

    {{-- Filter --}}
    <div class="flex flex-wrap justify-between items-end gap-4 mb-6">
        <form method="GET" action="{{ route('laporan.harian') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="tanggal" class="block text-sm font-medium text-gray-700">Pilih Tanggal:</label>
                <input type="date" name="tanggal" id="tanggal" value="{{ $tanggal }}"
                    class="border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm w-44 focus:outline-none focus:ring focus:ring-blue-500 bg-white text-gray-900">
            </div>
            <button type="submit"
                class="bg-sky-700 hover:bg-sky-400 text-white text-sm font-medium px-4 py-2 rounded-md shadow transition-all">
                Tampilkan
            </button>
        </form>

        <div class="flex gap-3">
            <form method="GET" action="{{ route('laporan.cetak') }}" target="_blank">
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
            </form>
            <button onclick="salinSemuaLaporan()"
                class="px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-green-600 transition">
                📋 Salin Semua Laporan
            </button>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="bg-white border border-gray-300 rounded-lg p-4 shadow-sm space-y-1 mb-6 text-gray-800">
        <p><strong>A. LAPORAN HARIAN JARINGAN OFFICE DAN SCADA (08:00 - 16:00 WIB)</strong></p>
        <p>I. Tiket Gangguan Jaringan SCADA : <strong>{{ $scadaCount > 0 ? $scadaCount . ' Tiket' : 'Nihil' }}</strong></p>
        <p>II. Tiket Gangguan Layanan WAN Office : <strong>{{ $wanCount > 0 ? $wanCount . ' Tiket' : 'Nihil' }}</strong></p>
        <p>III. Tiket Keluhan : <strong>{{ $keluhanCount > 0 ? $keluhanCount . ' Tiket' : 'Nihil' }}</strong></p>
    </div>

    <div class="space-y-6">
        {{-- Daftar Tiket SCADA --}}
        @if ($scadaCount > 0)
            <h3 class="text-lg font-semibold text-gray-700">I. Tiket Gangguan Jaringan SCADA : <strong>{{ $scadaCount > 0 ? $scadaCount . ' Tiket' : 'Nihil' }}</h3>
            <div class="space-y-4">
                @foreach ($scadaTikets as $index => $tiket)
                    <div class="bg-white border border-gray-300 rounded-lg p-4 shadow-sm space-y-1 text-gray-800">
                        <p><strong>{{ $index + 1 }}. [UIW SUMBAR] {{ $tiket->lokasi->lokasi }}</strong></p>
                        <p><span class="inline-block w-44 font-medium">Jenis Layanan</span>: {{ $tiket->jenis_gangguan ?? '-' }}</p>
                        <p><span class="inline-block w-44 font-medium">SID</span>: {{ $tiket->lokasi->sid }}</p>
                        <p><span class="inline-block w-44 font-medium">No Tiket</span>: {{ $tiket->no_tiket }}</p>
                        <p><span class="inline-block w-44 font-medium">Open Tiket</span>: {{ \Carbon\Carbon::parse($tiket->open_tiket)->format('d-m-Y (H:i \W\I\B)') }}</p>
                        <p><span class="inline-block w-44 font-medium">Stopclock</span>: {{ $tiket->stopclock ?? '-' }}</p>
                        <p>
                            <span class="inline-block w-44 font-medium">Link Up GSM</span>:
                            {{ $tiket->link_upGSM ? \Carbon\Carbon::parse($tiket->link_upGSM)->format('d-m-Y (H:i \W\I\B)') : '--:-- WIB' }}
                        </p>
                        <p>
                            <span class="inline-block w-44 font-medium">Link Up FO</span>:
                            {{ $tiket->link_up ? \Carbon\Carbon::parse($tiket->link_up)->format('d-m-Y (H:i \W\I\B)') : '--:-- WIB' }}
                        </p>
                        <p><span class="inline-block w-44 font-medium">Durasi</span>: {{ $tiket->durasi ?? '> 3 Jam' }}</p>
                        <p><span class="inline-block w-44 font-medium">Penyebab</span>: {{ $tiket->penyebab }}</p>
                        <p><span class="inline-block w-44 font-medium">Action</span>: {!! nl2br(e($tiket->action ?? '-')) !!}</p>
                        <p><span class="inline-block w-44 font-medium">Status Koneksi</span>: {{ $tiket->status_koneksi ?? '-' }}</p>
                        <p><span class="inline-block w-44 font-medium">Status Tiket</span>: {{ $tiket->status_tiket ?? '-' }}</p>
                        @if ($tiket->action_images)
                            <div class="mt-3">
                                <strong>Gambar Pendukung:</strong>
                                <div class="flex flex-wrap gap-3 mt-2">
                                    @foreach (json_decode($tiket->action_images, true) as $img)
                                        <a href="{{ asset('storage/' . $img) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $img) }}" class="h-24 rounded border border-gray-300 hover:scale-105 transition">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="pt-6">
                            <h2 class="text-md font-semibold mb-3">🕒 Rincian Stopclock</h2>
                            @if ($tiket->stopclocks && $tiket->stopclocks->count() > 0)
                                @php $totalMenit = 0; @endphp
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left border border-gray-200">
                                        <thead class="bg-gray-100 text-gray-700">
                                            <tr>
                                                <th class="px-4 py-2 border">No</th>
                                                <th class="px-4 py-2 border">Start Clock</th>
                                                <th class="px-4 py-2 border">Stop Clock</th>
                                                <th class="px-4 py-2 border">Alasan</th>
                                                <th class="px-4 py-2 border">Durasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tiket->stopclocks as $i => $sc)
                                                @php
                                                    $totalMenit += $sc->durasi ?? 0;
                                                    $jam = floor(($sc->durasi ?? 0) / 60);
                                                    $menit = ($sc->durasi ?? 0) % 60;
                                                @endphp
                                                <tr class="border-t">
                                                    <td class="px-4 py-2 border">{{ $i + 1 }}</td>
                                                    <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($sc->start_clock)->format('d M Y H:i') }}</td>
                                                    <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($sc->stop_clock)->format('d M Y H:i') }}</td>
                                                    <td class="px-4 py-2 border">{{ $sc->alasan ?? '-' }}</td>
                                                    <td class="px-4 py-2 border">
                                                        {{ $jam > 0 ? "$jam Jam " : '' }}{{ $menit }} Menit
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @php
                                                $totalJam = floor($totalMenit / 60);
                                                $totalSisaMenit = $totalMenit % 60;
                                            @endphp
                                            <tr class="font-semibold bg-gray-100 border-t">
                                                <td colspan="4" class="text-right px-4 py-2 border">Total Stopclock</td>
                                                <td class="px-4 py-2 border">
                                                    {{ $totalJam > 0 ? "$totalJam Jam " : '' }}{{ $totalSisaMenit }} Menit
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-gray-400 italic">Belum ada data stopclock yang tercatat.</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Daftar Tiket WAN Office --}}
        @if ($wanCount > 0)
            <h3 class="text-lg font-semibold text-gray-700">II. Tiket Gangguan Layanan WAN Office : <strong>{{ $wanCount > 0 ? $wanCount . ' Tiket' : 'Nihil' }}</h3>
            <div class="space-y-4">
                @foreach ($wanTikets as $index => $tiket)
                    <div class="bg-white border border-gray-300 rounded-lg p-4 shadow-sm space-y-1 text-gray-800">
                        <p><strong>{{ $index + 1 }}. [UIW SUMBAR] {{ $tiket->lokasi->lokasi }}</strong></p>
                        <p><span class="inline-block w-44 font-medium">Jenis Layanan</span>: {{ $tiket->jenis_gangguan ?? '-' }}</p>
                        <p><span class="inline-block w-44 font-medium">SID</span>: {{ $tiket->lokasi->sid }}</p>
                        <p><span class="inline-block w-44 font-medium">No Tiket</span>: {{ $tiket->no_tiket }}</p>
                        <p><span class="inline-block w-44 font-medium">Open Tiket</span>: {{ \Carbon\Carbon::parse($tiket->open_tiket)->format('d-m-Y (H:i \W\I\B)') }}</p>
                        <p><span class="inline-block w-44 font-medium">Stopclock</span>: {{ $tiket->stopclock ?? '-' }}</p>
                        <p>
                            <span class="inline-block w-44 font-medium">Link Up GSM</span>:
                            {{ $tiket->link_upGSM ? \Carbon\Carbon::parse($tiket->link_upGSM)->format('d-m-Y (H:i \W\I\B)') : '--:-- WIB' }}
                        </p>
                        <p>
                            <span class="inline-block w-44 font-medium">Link Up FO</span>:
                            {{ $tiket->link_up ? \Carbon\Carbon::parse($tiket->link_up)->format('d-m-Y (H:i \W\I\B)') : '--:-- WIB' }}
                        </p>
                        <p><span class="inline-block w-44 font-medium">Durasi</span>: {{ $tiket->durasi ?? '> 3 Jam' }}</p>
                        <p><span class="inline-block w-44 font-medium">Penyebab</span>: {{ $tiket->penyebab }}</p>
                        <p><span class="inline-block w-44 font-medium">Action</span>: {!! nl2br(e($tiket->action ?? '-')) !!}</p>
                        <p><span class="inline-block w-44 font-medium">Status Koneksi</span>: {{ $tiket->status_koneksi ?? '-' }}</p>
                        <p><span class="inline-block w-44 font-medium">Status Tiket</span>: {{ $tiket->status_tiket ?? '-' }}</p>
                        @if ($tiket->action_images)
                            <div class="mt-3">
                                <strong>Gambar Pendukung:</strong>
                                <div class="flex flex-wrap gap-3 mt-2">
                                    @foreach (json_decode($tiket->action_images, true) as $img)
                                        <a href="{{ asset('storage/' . $img) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $img) }}" class="h-24 rounded border border-gray-300 hover:scale-105 transition">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="pt-6">
                            <h2 class="text-md font-semibold mb-3">🕒 Rincian Stopclock</h2>
                            @if ($tiket->stopclocks && $tiket->stopclocks->count() > 0)
                                @php $totalMenit = 0; @endphp
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left border border-gray-200">
                                        <thead class="bg-gray-100 text-gray-700">
                                            <tr>
                                                <th class="px-4 py-2 border">No</th>
                                                <th class="px-4 py-2 border">Start Clock</th>
                                                <th class="px-4 py-2 border">Stop Clock</th>
                                                <th class="px-4 py-2 border">Alasan</th>
                                                <th class="px-4 py-2 border">Durasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tiket->stopclocks as $i => $sc)
                                                @php
                                                    $totalMenit += $sc->durasi ?? 0;
                                                    $jam = floor(($sc->durasi ?? 0) / 60);
                                                    $menit = ($sc->durasi ?? 0) % 60;
                                                @endphp
                                                <tr class="border-t">
                                                    <td class="px-4 py-2 border">{{ $i + 1 }}</td>
                                                    <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($sc->start_clock)->format('d M Y H:i') }}</td>
                                                    <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($sc->stop_clock)->format('d M Y H:i') }}</td>
                                                    <td class="px-4 py-2 border">{{ $sc->alasan ?? '-' }}</td>
                                                    <td class="px-4 py-2 border">
                                                        {{ $jam > 0 ? "$jam Jam " : '' }}{{ $menit }} Menit
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @php
                                                $totalJam = floor($totalMenit / 60);
                                                $totalSisaMenit = $totalMenit % 60;
                                            @endphp
                                            <tr class="font-semibold bg-gray-100 border-t">
                                                <td colspan="4" class="text-right px-4 py-2 border">Total Stopclock</td>
                                                <td class="px-4 py-2 border">
                                                    {{ $totalJam > 0 ? "$totalJam Jam " : '' }}{{ $totalSisaMenit }} Menit
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-gray-400 italic">Belum ada data stopclock yang tercatat.</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Daftar Tiket Keluhan --}}
        @if ($keluhanCount > 0)
            <h3 class="text-lg font-semibold text-gray-700">III. Tiket Keluhan : <strong>{{ $keluhanCount > 0 ? $keluhanCount . ' Tiket' : 'Nihil' }}</h3>
            <div class="space-y-4">
                @foreach ($keluhanTikets as $index => $tiket)
                    <div class="bg-white border border-gray-300 rounded-lg p-4 shadow-sm space-y-1 text-gray-800">
                        <p><strong>{{ $index + 1 }}. [UIW SUMBAR] {{ $tiket->lokasi->lokasi }}</strong></p>
                        <p><span class="inline-block w-44 font-medium">Jenis Layanan</span>: {{ $tiket->jenis_gangguan ?? '-' }}</p>
                        <p><span class="inline-block w-44 font-medium">SID</span>: {{ $tiket->lokasi->sid }}</p>
                        <p><span class="inline-block w-44 font-medium">No Tiket</span>: {{ $tiket->no_tiket }}</p>
                        <p><span class="inline-block w-44 font-medium">Open Tiket</span>: {{ \Carbon\Carbon::parse($tiket->open_tiket)->format('d-m-Y (H:i \W\I\B)') }}</p>
                        <p><span class="inline-block w-44 font-medium">Stopclock</span>: {{ $tiket->stopclock ?? '-' }}</p>
                        <p>
                            <span class="inline-block w-44 font-medium">Link Up GSM</span>:
                            {{ $tiket->link_upGSM ? \Carbon\Carbon::parse($tiket->link_upGSM)->format('d-m-Y (H:i \W\I\B)') : '--:-- WIB' }}
                        </p>
                        <p>
                            <span class="inline-block w-44 font-medium">Link Up FO</span>:
                            {{ $tiket->link_up ? \Carbon\Carbon::parse($tiket->link_up)->format('d-m-Y (H:i \W\I\B)') : '--:-- WIB' }}
                        </p>
                        <p><span class="inline-block w-44 font-medium">Durasi</span>: {{ $tiket->durasi ?? '> 3 Jam' }}</p>
                        <p><span class="inline-block w-44 font-medium">Penyebab</span>: {{ $tiket->penyebab }}</p>
                        <p><span class="inline-block w-44 font-medium">Action</span>: {!! nl2br(e($tiket->action ?? '-')) !!}</p>
                        <p><span class="inline-block w-44 font-medium">Status Koneksi</span>: {{ $tiket->status_koneksi ?? '-' }}</p>
                        <p><span class="inline-block w-44 font-medium">Status Tiket</span>: {{ $tiket->status_tiket ?? '-' }}</p>
                        @if ($tiket->action_images)
                            <div class="mt-3">
                                <strong>Gambar Pendukung:</strong>
                                <div class="flex flex-wrap gap-3 mt-2">
                                    @foreach (json_decode($tiket->action_images, true) as $img)
                                        <a href="{{ asset('storage/' . $img) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $img) }}" class="h-24 rounded border border-gray-300 hover:scale-105 transition">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="pt-6">
                            <h2 class="text-md font-semibold mb-3">🕒 Rincian Stopclock</h2>
                            @if ($tiket->stopclocks && $tiket->stopclocks->count() > 0)
                                @php $totalMenit = 0; @endphp
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left border border-gray-200">
                                        <thead class="bg-gray-100 text-gray-700">
                                            <tr>
                                                <th class="px-4 py-2 border">No</th>
                                                <th class="px-4 py-2 border">Start Clock</th>
                                                <th class="px-4 py-2 border">Stop Clock</th>
                                                <th class="px-4 py-2 border">Alasan</th>
                                                <th class="px-4 py-2 border">Durasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tiket->stopclocks as $i => $sc)
                                                @php
                                                    $totalMenit += $sc->durasi ?? 0;
                                                    $jam = floor(($sc->durasi ?? 0) / 60);
                                                    $menit = ($sc->durasi ?? 0) % 60;
                                                @endphp
                                                <tr class="border-t">
                                                    <td class="px-4 py-2 border">{{ $i + 1 }}</td>
                                                    <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($sc->start_clock)->format('d M Y H:i') }}</td>
                                                    <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($sc->stop_clock)->format('d M Y H:i') }}</td>
                                                    <td class="px-4 py-2 border">{{ $sc->alasan ?? '-' }}</td>
                                                    <td class="px-4 py-2 border">
                                                        {{ $jam > 0 ? "$jam Jam " : '' }}{{ $menit }} Menit
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @php
                                                $totalJam = floor($totalMenit / 60);
                                                $totalSisaMenit = $totalMenit % 60;
                                            @endphp
                                            <tr class="font-semibold bg-gray-100 border-t">
                                                <td colspan="4" class="text-right px-4 py-2 border">Total Stopclock</td>
                                                <td class="px-4 py-2 border">
                                                    {{ $totalJam > 0 ? "$totalJam Jam " : '' }}{{ $totalSisaMenit }} Menit
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-gray-400 italic">Belum ada data stopclock yang tercatat.</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
        {{-- Pesan kosong jika tidak ada tiket sama sekali --}}
        @if ($scadaCount === 0 && $wanCount === 0 && $keluhanCount === 0)
            <div class="text-center text-blue-600 bg-blue-50 border border-blue-200 px-6 py-10 rounded-lg shadow-sm mb-6">
                <p class="text-lg font-semibold">Tidak ada tiket keluhan</p>
                <p class="text-sm">Belum ada laporan gangguan untuk tanggal ini.</p>
            </div>
        @endif
    </div>

    {{-- Kegiatan Harian --}}
    <div class="bg-white border border-gray-300 rounded-lg p-4 shadow-sm space-y-3 mt-6 mb-10 text-gray-800">
        <p><strong>B. LAPORAN KEGIATAN HARIAN:</strong></p>
        @forelse ($kegiatanHarian as $kegiatan)
            <div class="bg-gray-50 border border-gray-300 rounded p-3 space-y-1">
                <p class="text-sm text-gray-800">
                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($kegiatan->tanggal)->translatedFormat('l, d F Y') }}
                </p>
                <p class="text-sm text-gray-800">
                    <strong>Judul:</strong> {{ $kegiatan->judul ?? '-' }}
                </p>
                <p class="text-sm text-gray-800"><strong>Siapa:</strong> {{ $kegiatan->siapa ?? '-' }}</p>
                <p class="text-sm text-gray-800"><strong>Apa:</strong> {{ $kegiatan->apa ?? '-' }}</p>
                <p class="text-sm text-gray-800"><strong>Kapan:</strong> {{ $kegiatan->kapan ?? '-' }}</p>
                <p class="text-sm text-gray-800"><strong>Dimana:</strong> {{ $kegiatan->dimana ?? '-' }}</p>
                <p class="text-sm text-gray-800"><strong>Mengapa:</strong> {{ $kegiatan->mengapa ?? '-' }}</p>
                <p class="text-sm text-gray-800"><strong>Bagaimana:</strong> {{ $kegiatan->bagaimana ?? '-' }}</p>
                <p class="text-sm text-gray-800 font-medium mt-2">Deskripsi Kegiatan:</p>
                <p class="text-sm text-gray-800 whitespace-pre-line">{{ $kegiatan->kegiatan }}</p>
            </div>
        @empty
            <div class="text-center text-blue-600 bg-blue-50 border border-blue-200 px-6 py-8 rounded-lg">
                <p class="text-lg font-semibold">Tidak ada laporan kegiatan</p>
                <p class="text-sm">Belum ada laporan kegiatan untuk tanggal ini.</p>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    function salinSemuaLaporan() {
        // Pisahkan tiket di JavaScript juga untuk memastikan urutan yang benar
        const scadaTikets = @json($tikets->where('jenis_gangguan', 'SCADA')->values()->all());
        const wanTikets = @json($tikets->where('jenis_gangguan', 'WAN Office')->values()->all());
        const keluhanTikets = @json($tikets->where('jenis_gangguan', 'Keluhan')->values()->all());
        const kegiatanHarian = @json($kegiatanHarian);

        const jumlahTiket = scadaTikets.length + wanTikets.length + keluhanTikets.length;
        const jumlahKegiatan = kegiatanHarian.length;

        if (jumlahTiket === 0 && jumlahKegiatan === 0) {
            alert("Tidak ada laporan untuk disalin.");
            return;
        }

        let teks = "";
        teks += "Laporan Harian Jaringan Office & SCADA\n";
        teks += "Tanggal           : {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}\n\n";

        teks += "A. Laporan Harian (08:00 - 16:00 WIB)\n";
        teks += "I.  Gangguan Jaringan SCADA : " + (scadaTikets.length > 0 ? scadaTikets.length + ' Tiket' : 'Nihil') + "\n";
        teks += "II. Layanan WAN Office      : " + (wanTikets.length > 0 ? wanTikets.length + ' Tiket' : 'Nihil') + "\n";
        teks += "III.Tiket Keluhan           : " + (keluhanTikets.length > 0 ? keluhanTikets.length + ' Tiket' : 'Nihil') + "\n\n";

        // Fungsi helper untuk memformat tiket
        function formatTiket(tiket, index) {
            let tiketText = `${index + 1}. [UIW SUMBAR] ${tiket.lokasi.lokasi || '-'}\n`;
            tiketText += `SID               : ${tiket.lokasi.sid || '-'}\n`;
            tiketText += `No Tiket          : ${tiket.no_tiket || '-'}\n`;
            tiketText += `Open Tiket        : ${tiket.open_tiket ? new Date(tiket.open_tiket).toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'}).replace(/\./g, '-') : '-'} WIB\n`;
            tiketText += `Stopclock         : ${tiket.stopclocks.length > 0 ? tiket.stopclocks.length + 'x stopclock' : '-'}\n`;
            tiketText += `Link Up GSM       : ${tiket.link_upGSM ? new Date(tiket.link_upGSM).toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'}).replace(/\./g, '-') : '-'} WIB\n`;
            tiketText += `Jenis Gangguan    : ${tiket.jenis_gangguan || '-'}\n`;
            tiketText += `Link Up FO        : ${tiket.link_up ? new Date(tiket.link_up).toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'}).replace(/\./g, '-') : '-'} WIB\n`;
            tiketText += `Penyebab          : ${tiket.penyebab || '-'}\n`;
            tiketText += `Durasi            : ${tiket.durasi || '-'}\n`;
            tiketText += `Action            : ${tiket.action ? tiket.action.replace(/(\r\n|\n|\r)/gm, ' ') : '-'}\n`;

            // Status Koneksi
            let statusKoneksi = tiket.status_koneksi || '-';
            if (statusKoneksi === 'Up') statusKoneksi += ' 🟢';
            else if (statusKoneksi === 'GSM') statusKoneksi += ' 🟡';
            else if (statusKoneksi === 'Down') statusKoneksi += ' 🔴';
            tiketText += `Status Koneksi    : ${statusKoneksi}\n`;

            // Status Tiket
            let statusTiket = tiket.status_tiket || '-';
            if (statusTiket === 'Proses') statusTiket += ' ⏳';
            else if (statusTiket === 'Selesai') statusTiket += ' ✅';
            tiketText += `Status Tiket      : ${statusTiket}\n`;

            // Rincian Stopclock
            if (tiket.stopclocks && tiket.stopclocks.length > 0) {
                let totalMenit = 0;
                tiketText += `\n🕒 Rincian Stopclock:\n`;
                tiket.stopclocks.forEach((sc, i) => {
                    const start = new Date(sc.start_clock);
                    const stop = new Date(sc.stop_clock);
                    const durasiMenit = sc.durasi || 0;
                    totalMenit += durasiMenit;
                    const jam = Math.floor(durasiMenit / 60);
                    const menit = durasiMenit % 60;
                    tiketText += `${i + 1}. Start : ${start.toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'}).replace(/\./g, '-')}, Stop : ${stop.toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'}).replace(/\./g, '-')}\n`;
                    tiketText += `   Alasan : ${sc.alasan || '-'}\n`;
                    tiketText += `   Durasi : ${jam > 0 ? jam + ' Jam ' : ''}${menit} Menit\n`;
                });
                const totalJam = Math.floor(totalMenit / 60);
                const totalSisaMenit = totalMenit % 60;
                tiketText += `Total Stopclock   : ${totalJam > 0 ? totalJam + ' Jam ' : ''}${totalSisaMenit} Menit\n`;
            }
            tiketText += `━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n`;
            return tiketText;
        }

        // Tambahkan tiket SCADA
        if (scadaTikets.length > 0) {
            teks += "I. Tiket Gangguan Jaringan SCADA\n";
            scadaTikets.forEach((tiket, index) => {
                teks += formatTiket(tiket, index);
            });
        }
        
        // Tambahkan tiket WAN Office
        if (wanTikets.length > 0) {
            teks += "II. Tiket Gangguan Layanan WAN Office\n";
            wanTikets.forEach((tiket, index) => {
                teks += formatTiket(tiket, index);
            });
        }
        
        // Tambahkan tiket Keluhan
        if (keluhanTikets.length > 0) {
            teks += "III. Tiket Keluhan\n";
            keluhanTikets.forEach((tiket, index) => {
                teks += formatTiket(tiket, index);
            });
        }

        teks += "B. Laporan Kegiatan Harian:\n\n";
        if (kegiatanHarian.length > 0) {
            kegiatanHarian.forEach((kegiatan, index) => {
                teks += `${index + 1}. ${kegiatan.judul || '-'}\n`;
                teks += `Waktu             : ${new Date(kegiatan.tanggal).toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric'}).replace(/\./g, '-')}\n`;
                teks += `Siapa             : ${kegiatan.siapa || '-'}\n`;
                teks += `Apa               : ${kegiatan.apa || '-'}\n`;
                teks += `Kapan             : ${kegiatan.kapan || '-'}\n`;
                teks += `Dimana            : ${kegiatan.dimana || '-'}\n`;
                teks += `Mengapa           : ${kegiatan.mengapa || '-'}\n`;
                teks += `Bagaimana         : ${kegiatan.bagaimana || '-'}\n`;
                teks += `Deskripsi         : ${kegiatan.kegiatan ? kegiatan.kegiatan.replace(/(\r\n|\n|\r)/gm, ' ') : '-'}\n`;
                teks += `━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n`;
            });
        } else {
            teks += "Tidak ada laporan kegiatan untuk tanggal ini.\n\n";
        }

        let laporanFinal = "```" + teks + "```";

        navigator.clipboard.writeText(laporanFinal)
            .then(() => alert("Laporan berhasil disalin ke clipboard."))
            .catch(() => alert("Gagal menyalin laporan."));
    }
</script>
@endpush

@endsection