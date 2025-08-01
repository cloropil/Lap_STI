@extends('layouts.app')

@section('title', 'Input Tiket Baru')

@section('content')
@php
    $user = Auth::user();
    $role = $user?->role ?? 'guest';
@endphp

<div class="max-w-6xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
    <div class="bg-blue-500 px-6 py-4 text-white text-lg font-semibold flex items-center">
        Input Tiket Gangguan Baru
    </div>

    @if ($errors->any())
        <div class="mx-6 my-4 p-3 bg-red-50 border border-red-200 text-sm text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (in_array($role, ['superadmin', 'admin', 'staff']))
        <form action="{{ route('inputtiket.store') }}" method="POST" enctype="multipart/form-data" class="px-6 py-6 space-y-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-10">
                {{-- KIRI --}}
                <div class="space-y-5">
                    <div>
                        <label for="lokasi_id" class="block text-sm text-gray-500 font-medium mb-1">Lokasi</label>
                        <select name="lokasi_id" id="lokasi_id" required class="w-full border border-gray-300 rounded-md text-sm">
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach ($lokasis as $lokasi)
                                <option value="{{ $lokasi->id }}" @selected(old('lokasi_id') == $lokasi->id)>{{ $lokasi->lokasi }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="sid" class="block text-sm text-gray-500 font-medium mb-1">SID</label>
                        <input type="text" id="sid" name="sid" readonly value="{{ old('sid') }}" class="w-full bg-gray-100 border border-gray-300 rounded-md text-sm">
                    </div>

                    <x-validasi label="No Tiket" name="no_tiket" required value="{{ old('no_tiket') }}" />

                    <div>
                        <label for="open_tiket" class="block text-sm text-gray-500 font-medium mb-1">Open Tiket</label>
                        <input type="datetime-local" id="open_tiket" name="open_tiket" required value="{{ old('open_tiket') }}" class="w-full border border-gray-300 rounded-md text-sm">
                    </div>

                    <div>
                        <label for="link_up" class="block text-sm text-gray-500 font-medium mb-1">Link Up</label>
                        <input type="datetime-local" id="link_up" name="link_up" value="{{ old('link_up') }}" class="w-full border border-gray-300 rounded-md text-sm">
                    </div>

                    <div>
                        <label for="durasi" class="block text-sm text-gray-500 font-medium mb-1">Durasi</label>
                        <input type="text" id="durasi" name="durasi" readonly value="{{ old('durasi') }}" class="w-full bg-gray-100 border border-gray-300 rounded-md text-sm">
                        <div id="durasi_warning" class="text-sm text-red-600 mt-1 hidden">
                            Durasi menjadi 0 menit. Periksa kembali stopclock Anda!
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">⏱️ Rincian Stopclock</h3>
                        <div id="stopclock-wrapper" class="space-y-2"></div>
                        <button type="button" onclick="addStopclockRow()" class="mt-2 text-sm text-blue-600 hover:underline">+ Tambah Stopclock</button>
                    </div>
                </div>

                {{-- KANAN --}}
                <div class="space-y-5">
                    @if (in_array($role, ['superadmin', 'admin']))
                        <div>
                            <label for="status_tiket" class="block text-sm text-gray-500 font-medium mb-1">Status Tiket</label>
                            <select name="status_tiket" id="status_tiket" required class="w-full border border-gray-300 rounded-md text-sm">
                                <option value="">-- Pilih Status Tiket --</option>
                                <option value="Proses" @selected(old('status_tiket') == 'Proses')>Proses</option>
                                <option value="Selesai" @selected(old('status_tiket') == 'Selesai')>Selesai</option>
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="status_tiket" value="Proses">
                    @endif

                    <div id="pesan_status" class="hidden p-3 text-sm text-yellow-800 bg-yellow-50 border border-yellow-200 rounded">
                        Tim masih proses backup GSM & Recovery FO belum bisa dilakukan karena menunggu progress tim PLN selesai.
                    </div>

                    <div>
                        <label for="jenis_gangguan" class="block text-sm text-gray-500 font-medium mb-1">Jenis Gangguan</label>
                        <select name="jenis_gangguan" id="jenis_gangguan" required class="w-full border border-gray-300 rounded-md text-sm">
                            <option value="">-- Pilih Jenis Gangguan --</option>
                            <option value="SCADA" @selected(old('jenis_gangguan') == 'SCADA')>Jaringan SCADA</option>
                            <option value="WAN" @selected(old('jenis_gangguan') == 'WAN')>WAN Office</option>
                        </select>
                    </div>

                    <div>
                        <label for="status_koneksi" class="block text-sm text-gray-500 font-medium mb-1">Status Koneksi</label>
                        <div class="flex items-center gap-3">
                            <select name="status_koneksi" id="status_koneksi" required class="w-full border border-gray-300 rounded-md text-sm">
                                <option value="">-- Pilih Status Koneksi --</option>
                                <option value="Terhubung" @selected(old('status_koneksi') == 'Terhubung')>Terhubung</option>
                                <option value="Terkendala" @selected(old('status_koneksi') == 'Terkendala')>Terkendala</option>
                                <option value="Putus" @selected(old('status_koneksi') == 'Putus')>Putus</option>
                            </select>
                            <span id="status_indicator" class="w-3 h-3 rounded-full bg-gray-300 border border-gray-400"></span>
                        </div>
                    </div>

                    <x-validasi label="Penyebab" name="penyebab" textarea :required="false">{{ old('penyebab') }}</x-validasi>
                    <x-validasi label="Action" name="action" textarea>{{ old('action') }}</x-validasi>

                    <div>
                        <label class="block text-sm text-gray-500 font-medium mb-1">Gambar Pendukung (maks. 15)</label>
                        <input type="file" id="action_images" name="action_images[]" multiple accept="image/*" class="w-full text-sm border border-gray-300 rounded-md">
                        <div id="preview-container" class="flex flex-wrap gap-3 mt-3"></div>
                    </div>
                </div>
            </div>

            <div class="pt-8 flex justify-between">
                <a href="{{ route('inputtiket.index') }}" class="bg-sky-700 hover:bg-sky-400 text-white text-sm font-medium px-4 py-2 rounded-md shadow inline-flex items-center gap-2">
                    Kembali</a>
                <button type="submit" class="bg-sky-700 hover:bg-sky-400 text-white text-sm font-medium px-4 py-2 rounded-md shadow inline-flex items-center gap-2">
                    Simpan Tiket</button>
            </div>
        </form>
    @else
        <div class="max-w-2xl mx-auto bg-red-100 text-red-700 rounded p-6 mt-6">
            <h2 class="text-lg font-semibold">Akses Ditolak</h2>
            <p>Anda tidak memiliki izin untuk input tiket gangguan baru.</p>
        </div>
    @endif
</div>

<script>
let selectedFiles = [];

document.getElementById('action_images').addEventListener('change', function (event) {
    const files = Array.from(event.target.files);

    files.forEach(file => {
        if (!file.type.startsWith('image/')) return;

        selectedFiles.push(file);

        const reader = new FileReader();
        reader.onload = function (e) {
            const container = document.createElement('div');
            container.className = "relative w-24 h-24 border rounded overflow-hidden";

            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = "object-cover w-full h-full";

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.innerText = '×';
            btn.className = "absolute top-0 right-0 bg-red-600 text-white w-5 h-5 text-xs flex items-center justify-center rounded-bl cursor-pointer";
            btn.onclick = function () {
                const index = Array.from(container.parentNode.children).indexOf(container);
                selectedFiles.splice(index, 1);
                container.remove();
                refreshInputFiles();
            };

            container.appendChild(img);
            container.appendChild(btn);
            document.getElementById('preview-container').appendChild(container);
        };
        reader.readAsDataURL(file);
    });

    refreshInputFiles();
});

function refreshInputFiles() {
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    document.getElementById('action_images').files = dataTransfer.files;
}

function updateStatusColor() {
    const status = document.getElementById('status_koneksi').value;
    const indicator = document.getElementById('status_indicator');
    indicator.classList.remove('bg-green-500', 'bg-yellow-400', 'bg-red-600', 'bg-gray-300');

    switch (status) {
        case 'Terhubung': indicator.classList.add('bg-green-500'); break;
        case 'Terkendala': indicator.classList.add('bg-yellow-400'); break;
        case 'Putus': indicator.classList.add('bg-red-600'); break;
        default: indicator.classList.add('bg-gray-300'); break;
    }
}
window.addEventListener('DOMContentLoaded', updateStatusColor);
document.getElementById('status_koneksi')?.addEventListener('change', updateStatusColor);

document.getElementById('lokasi_id')?.addEventListener('change', function () {
    const lokasiId = this.value;
    const sidInput = document.getElementById('sid');
    if (!lokasiId) return sidInput.value = '';
    fetch(`/lokasi/${lokasiId}/sid`)
        .then(res => res.ok ? res.json() : Promise.reject())
        .then(data => sidInput.value = data.sid ?? '')
        .catch(() => { sidInput.value = ''; alert('Gagal mengambil SID.'); });
});

function addStopclockRow() {
    const wrapper = document.getElementById('stopclock-wrapper');
    const row = document.createElement('div');
    row.className = 'stopclock-row flex items-center gap-2';

    row.innerHTML = `
        <input type="datetime-local" name="stopclocks[start_clock][]" class="start border border-gray-300 rounded-md text-sm" required>
        <span class="text-gray-400">➡️</span>
        <input type="datetime-local" name="stopclocks[stop_clock][]" class="end border border-gray-300 rounded-md text-sm" required>
        <input type="text" name="stopclocks[alasan][]" placeholder="Alasan" class="alasan border border-gray-300 rounded-md text-sm" required>
        <button type="button" onclick="this.parentElement.remove(); hitungDurasi();" class="text-red-500 hover:text-red-700 text-sm">🗑️</button>
    `;

    row.querySelector('.start').addEventListener('change', hitungDurasi);
    row.querySelector('.end').addEventListener('change', hitungDurasi);
    wrapper.appendChild(row);
    hitungDurasi();
}

function hitungDurasi() {
    const open = document.getElementById('open_tiket').value;
    const linkUp = document.getElementById('link_up').value;
    const durasiInput = document.getElementById('durasi');
    const warning = document.getElementById('durasi_warning');

    if (!open || !linkUp) return durasiInput.value = '';

    const t1 = new Date(open);
    const t2 = new Date(linkUp);
    let durasi = (t2 - t1) / 60000;

    if (isNaN(durasi) || durasi < 0) {
        durasiInput.value = '0 menit';
        warning.classList.remove('hidden');
        return;
    }

    let stopclock = 0;
    document.querySelectorAll('.stopclock-row').forEach(row => {
        const s = new Date(row.querySelector('.start').value);
        const e = new Date(row.querySelector('.end').value);
        const d = (e - s) / 60000;
        if (!isNaN(d) && d > 0) stopclock += d;
    });

    durasi -= stopclock;
    if (durasi < 0) durasi = 0;

    const hari = Math.floor(durasi / 1440);
    durasi %= 1440;
    const jam = Math.floor(durasi / 60);
    const menit = Math.floor(durasi % 60);

    durasiInput.value = `${hari ? hari + ' hari ' : ''}${jam ? jam + ' jam ' : ''}${menit || (!hari && !jam) ? menit + ' menit' : ''}`.trim();
    warning.classList.add('hidden');
}

document.getElementById('open_tiket')?.addEventListener('change', hitungDurasi);
document.getElementById('link_up')?.addEventListener('change', hitungDurasi);
</script>
@endsection
