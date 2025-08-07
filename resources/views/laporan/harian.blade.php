@extends('layouts.app')

@section('title', 'Laporan Harian')

@section('content')
@php
    $scadaCount = $tikets->where('jenis_gangguan', 'SCADA')->count();
    $wanCount = $tikets->where('jenis_gangguan', 'WAN')->count();
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
                <!-- <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-md shadow-sm">
                    Cetak Laporan
                </button> -->
            </form>
            <button type="button" onclick="salinSemuaLaporan()"
                class="border border-gray-300 hover:bg-gray-100 text-gray-700 text-sm font-medium px-4 py-2 rounded-md">
                Salin Semua Laporan
            </button>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="bg-white border border-gray-300 rounded-lg p-4 shadow-sm space-y-1 mb-6 text-gray-800">
        <p><strong>A. LAPORAN HARIAN JARINGAN OFFICE DAN SCADA (08:00 - 16:00 WIB)</strong></p>
        <p>I. Gangguan Jaringan SCADA : <strong>{{ $scadaCount > 0 ? $scadaCount . ' Tiket' : 'Nihil' }}</strong></p>
        <p>II. Layanan WAN Office      : <strong>{{ $wanCount > 0 ? $wanCount . ' Tiket' : 'Nihil' }}</strong></p>
        <p>III. Tiket Keluhan          : <strong>{{ $tikets->count() }}</strong></p>
    </div>

    {{-- Daftar Tiket --}}
    @forelse ($tikets as $index => $tiket)
        <div class="bg-white border border-gray-300 rounded-lg p-4 shadow-sm space-y-1 mb-4 text-gray-800">
            <p><strong>{{ $index + 1 }}. [UIW SUMBAR] {{ $tiket->lokasi->lokasi }}</strong></p>
            <p><span class="inline-block w-44 font-medium">SID</span>: {{ $tiket->lokasi->sid }}</p>
            <p><span class="inline-block w-44 font-medium">No Tiket</span>: {{ $tiket->no_tiket }}</p>
            <p><span class="inline-block w-44 font-medium">Open Tiket</span>: {{ \Carbon\Carbon::parse($tiket->open_tiket)->format('d-m-Y (H:i \W\I\B)') }}</p>
            <p><span class="inline-block w-44 font-medium">Stopclock</span>: {{ $tiket->stopclock ?? '-' }}</p>
            <p><span class="inline-block w-44 font-medium">Link Up</span>: {{ $tiket->link_up ? \Carbon\Carbon::parse($tiket->link_up)->format('d-m-Y (H:i \W\I\B)') : '--:-- WIB' }}</p>
            <p><span class="inline-block w-44 font-medium">Durasi</span>: {{ $tiket->durasi ?? '> 3 Jam' }}</p>
            <p><span class="inline-block w-44 font-medium">Jenis Gangguan</span>: {{ $tiket->jenis_gangguan ?? '-' }}</p>
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
    @empty
        <div class="text-center text-blue-600 bg-blue-50 border border-blue-200 px-6 py-10 rounded-lg shadow-sm mb-6">
            <p class="text-lg font-semibold">Tidak ada tiket keluhan</p>
            <p class="text-sm">Belum ada laporan gangguan untuk tanggal ini.</p>
        </div>
    @endforelse

    {{-- Kegiatan Harian --}}
    <div class="bg-white border border-gray-300 rounded-lg p-4 shadow-sm space-y-3 mb-10 text-gray-800">
        <p><strong>B. LAPORAN KEGIATAN HARIAN:</strong></p>
        @forelse ($kegiatanHarian as $kegiatan)
            <div class="bg-gray-50 border border-gray-300 rounded p-3 space-y-1">
                <p class="text-sm text-gray-800">
                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($kegiatan->tanggal)->translatedFormat('l, d F Y') }}
                </p>
                <p class="text-sm text-gray-800">
                    <strong>Judul:</strong> {{ $kegiatan->judul ?? '-' }}
                </p>
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
    const jumlahTiket = {{ $tikets->count() }};
    const jumlahKegiatan = {{ $kegiatanHarian->count() }};

    if (jumlahTiket === 0 && jumlahKegiatan === 0) {
        alert("Tidak ada laporan untuk disalin.");
        return;
    }

    let teks = '';
    teks += `Laporan Harian Jaringan Office & SCADA\n`;
    teks += `Tanggal: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}\n\n`;

    teks += `A. Laporan Harian (08:00 - 16:00 WIB)\n`;
    teks += `I. Gangguan Jaringan SCADA : {{ $scadaCount > 0 ? $scadaCount . ' Tiket' : 'Nihil' }}\n`;
    teks += `II. Layanan WAN Office : {{ $wanCount > 0 ? $wanCount . ' Tiket' : 'Nihil' }}\n`;
    teks += `III. Tiket Keluhan : {{ $tikets->count() }}\n\n`;

    @foreach ($tikets as $index => $tiket)
        teks += `{{ $index + 1 }}. [UIW SUMBAR] {{ $tiket->lokasi->lokasi }}\n`;
        teks += `SID\t            : {{ $tiket->lokasi->sid }}\n`;
        teks += `No Tiket\t            : {{ $tiket->no_tiket }}\n`;
        teks += `Open Tiket         : {{ \Carbon\Carbon::parse($tiket->open_tiket)->format('d-m-Y (H:i WIB)') }}\n`;
        teks += `Stopclock           : {{ $tiket->stopclock ?? '-' }}\n`;
        teks += `Link Up\t            : {{ $tiket->link_up ? \Carbon\Carbon::parse($tiket->link_up)->format('d-m-Y (H:i WIB)') : '--:-- WIB' }}\n`;
        teks += `Durasi\t            : {{ $tiket->durasi ?? '> 3 Jam' }}\n`;
        teks += `Jenis Gangguan : {{ $tiket->jenis_gangguan ?? '-' }}\n`;
        teks += `Penyebab           : {{ $tiket->penyebab }}\n`;
        teks += `Action\t            : {!! str_replace(["\r", "\n"], ["", '\\n'], e($tiket->action ?? '-')) !!}\n`;

        {{-- Modifikasi untuk Status Koneksi --}}
        @if ($tiket->status_koneksi == 'Terhubung')
            teks += `Status Koneksi   : Terhubung 🟢\n`;
        @elseif ($tiket->status_koneksi == 'Terkendala')
            teks += `Status Koneksi   : Terkendala 🟡\n`;
        @elseif ($tiket->status_koneksi == 'Putus')
            teks += `Status Koneksi   : Putus 🔴\n`;
        @else
            teks += `Status Koneksi   : {{ $tiket->status_koneksi ?? '-' }}\n`;
        @endif

        {{-- Modifikasi untuk Status Tiket --}}
        @if ($tiket->status_tiket == 'Proses')
            teks += `Status Tiket        : Proses ⏳\n`;
        @elseif ($tiket->status_tiket == 'Selesai')
            teks += `Status Tiket        : Selesai ✅\n`;
        @else
            teks += `Status Tiket        : {{ $tiket->status_tiket ?? '-' }}\n`;
        @endif

        @if ($tiket->stopclocks && $tiket->stopclocks->count() > 0)
            teks += `\n🕒 Rincian Stopclock:\n`;
            @php $totalMenit = 0; @endphp
            @foreach ($tiket->stopclocks as $i => $sc)
                @php
                    $start = \Carbon\Carbon::parse($sc->start_clock);
                    $stop = \Carbon\Carbon::parse($sc->stop_clock);
                    $durasiMenit = $sc->durasi ?? 0;
                    $totalMenit += $durasiMenit;
                    $jam = floor($durasiMenit / 60);
                    $menit = $durasiMenit % 60;
                @endphp
                teks += `{{ $i + 1 }}. Start: {{ $start->format('d-m-Y H:i') }}, Stop: {{ $stop->format('d-m-Y H:i') }}\n`;
                teks += `   Alasan : {{ $sc->alasan }}\n`;
                teks += `   Durasi : {{ $jam > 0 ? $jam . ' Jam ' : '' }}{{ $menit }} Menit\n`;
                teks += `------------------------------------\n`;
            @endforeach
            @php
                $totalJam = floor($totalMenit / 60);
                $totalSisaMenit = $totalMenit % 60;
            @endphp
            teks += `Total Stopclock: {{ $totalJam > 0 ? $totalJam . ' Jam ' : '' }}{{ $totalSisaMenit }} Menit\n`;
            teks += `━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n`;
        @endif

        teks += `\n`;
    @endforeach

    teks += `B. Laporan Kegiatan Harian:\n\n`;
    @foreach ($kegiatanHarian as $index => $kegiatan)
        teks += `{{ $index + 1 }}. {{ $kegiatan->judul }}\n`;
        teks += `Waktu\t    : {{ \Carbon\Carbon::parse($kegiatan->tanggal)->format('d-m-Y') }} | {{ $kegiatan->waktu }} WIB\n`;
        teks += `Siapa\t    : {{ $kegiatan->siapa ?? '-' }}\n`;
        teks += `Deskripsi\t    :\n{!! str_replace(["\r", "\n"], ["", '\\n'], e($kegiatan->kegiatan)) !!}\n`;
        teks += `━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n`;
    @endforeach


    const textarea = document.createElement("textarea");
    textarea.value = teks;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand("copy");
    document.body.removeChild(textarea);

    alert("Laporan berhasil disalin ke clipboard.");
}
</script>
@endpush

@endsection
