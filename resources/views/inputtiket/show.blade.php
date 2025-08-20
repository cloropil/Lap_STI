@extends('layouts.app')

@section('title', 'Detail Tiket')

@section('content')
<div class="max-w-5xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
    <div class="bg-blue-500 px-6 py-4 text-white text-lg font-semibold flex items-center">
        📄 Detail Tiket: {{ $tiket->no_tiket }}
    </div>
    
    <div class="p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
            <div>
                <p class="text-gray-500 font-medium">No Tiket</p>
                <p>{{ $tiket->no_tiket }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium">Open Tiket</p>
                <p>{{ $tiket->open_tiket ? \Carbon\Carbon::parse($tiket->open_tiket)->format('d M Y H:i') : '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium">Layanan</p>
                <p>{{ $tiket->lokasi->lokasi ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium">Link Up GSM</p>
                <p>{{ $tiket->link_upGSM ? \Carbon\Carbon::parse($tiket->link_upGSM)->format('d M Y H:i') : '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium">SID</p>
                <p>{{ $tiket->lokasi->sid ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium">Link Up FO</p>
                <p>{{ $tiket->link_up ? \Carbon\Carbon::parse($tiket->link_up)->format('d M Y H:i') : '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium">Jenis Layanan</p>
                <p>{{ $tiket->lokasi->product ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium">Durasi GSM</p>
                <p>{{ $tiket->durasi_GSM ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium">Produk</p>
                <p>{{ $tiket->lokasi->kategori_layanan ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium">Durasi FO</p>
                <p>{{ $tiket->durasi ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium">Bandwidth</p>
                <p>{{ $tiket->lokasi->bandwith ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium">Penyebab</p>
                <p>{{ $tiket->penyebab }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium">Jenis Gangguan</p>
                <p>{{ $tiket->jenis_gangguan ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium">Action</p>
                <p>{{ $tiket->action }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium">Stopclock</p>
                <p>{{ $tiket->stopclocks->count() > 0 ? $tiket->stopclocks->count() . 'x stopclock' : '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium">Status Koneksi</p>
                <div class="flex items-center gap-2">
                    <span 
                        class="inline-block w-3 h-3 rounded-full 
                        @if ($tiket->status_koneksi === 'Up') bg-green-500
                        @elseif ($tiket->status_koneksi === 'GSM') bg-yellow-400
                        @elseif ($tiket->status_koneksi === 'Down') bg-red-500
                        @else bg-gray-300 @endif">
                    </span>
                    <span>
                        @if($tiket->status_koneksi === 'Up')
                            Link Up FO
                        @elseif($tiket->status_koneksi === 'GSM')
                            Link Up GSM
                        @elseif($tiket->status_koneksi === 'Down')
                            Down
                        @else
                            {{ $tiket->status_koneksi ?? '-' }}
                        @endif
                    </span>
                </div>
            </div>
            <div>
                <p class="text-gray-500 font-medium">Jumlah Gambar</p>
                <p>{{ count($tiket->action_images_array ?? []) }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium">Status Tiket</p>
                <p>
                    @if($tiket->status_tiket == 'Proses')
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded-full">Open</span>
                    @elseif($tiket->status_tiket == 'Selesai')
                        <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">Closed</span>
                    @else
                        <span class="bg-gray-200 text-gray-800 text-xs font-semibold px-2 py-1 rounded-full">{{ $tiket->status_tiket }}</span>
                    @endif
                </p>
            </div>
            <div class="md:col-span-2">
                <p class="text-gray-500 font-medium">Gambar Pendukung</p>
                @if ($tiket->action_images)
                    <div class="flex flex-wrap gap-3 mt-2">
                        @foreach (json_decode($tiket->action_images, true) as $img)
                            <img
                                src="{{ asset('storage/' . $img) }}"
                                alt="Gambar Pendukung"
                                class="h-24 border rounded shadow-sm cursor-pointer"
                                onclick="zoomImage('{{ asset('storage/' . $img) }}')"
                            >
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 italic">Tidak ada gambar</p>
                @endif
            </div>
        </div>

        {{-- Rincian Stopclock --}}
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

        <div class="text-end mt-6">
            <a href="{{ route('inputtiket.index') }}" 
            class="bg-sky-700 hover:bg-sky-400 text-white text-sm font-medium px-4 py-2 rounded-md shadow inline-flex items-center gap-2 transition-all duration-200 ease-in-out">
                Kembali
            </a>
        </div>
    </div>
</div>

{{-- Modal Zoom Gambar --}}
<div id="modal-zoom" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 hidden">
    <div class="relative max-w-[90%] max-h-[90%]">
        <img id="modal-img" src="" alt="Zoom Image" class="max-w-full max-h-full rounded shadow-xl">
        <button
            onclick="closeZoom()"
            class="absolute top-[-10px] right-[-10px] text-white bg-red-600 hover:bg-red-700 rounded-full px-3 py-1 text-sm"
        >✕</button>
    </div>
</div>

<script>
    function zoomImage(src) {
        const modal = document.getElementById('modal-zoom');
        const modalImg = document.getElementById('modal-img');
        modalImg.src = src;
        modal.classList.remove('hidden');
    }

    function closeZoom() {
        const modal = document.getElementById('modal-zoom');
        modal.classList.add('hidden');
        document.getElementById('modal-img').src = '';
    }

    document.getElementById('modal-zoom').addEventListener('click', function(e) {
        if(e.target === this) {
            closeZoom();
        }
    });
</script>
@endsection
