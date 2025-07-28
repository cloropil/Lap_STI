@extends('layouts.app')

@section('title', 'Edit Tiket')

@section('content')
<div class="max-w-5xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
    <div class="bg-blue-500 px-6 py-4 text-white text-lg font-semibold flex items-center">
        Edit Tiket: {{ $tiket->no_tiket }}
    </div>

    <form action="{{ route('inputtiket.update', $tiket->id) }}" method="POST" enctype="multipart/form-data" class="px-6 py-6 space-y-8">
        @csrf
        @method('PUT')

        <input type="hidden" name="deleted_images" id="deleted_images">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-10">
            {{-- KIRI --}}
            <div class="space-y-5">
                <div>
                    <label class="block text-sm text-gray-500 font-medium mb-1">No Tiket</label>
                    <input type="text" name="no_tiket" value="{{ $tiket->no_tiket }}" readonly class="w-full bg-gray-100 border border-gray-300 rounded-md text-sm">
                </div>

                <div>
                    <label class="block text-sm text-gray-500 font-medium mb-1">Lokasi</label>
                    <select name="lokasi_id" class="w-full border @error('lokasi_id') border-red-500 @else border-gray-300 @enderror rounded-md text-sm">
                        @foreach ($lokasis as $lokasi)
                            <option value="{{ $lokasi->id }}" {{ old('lokasi_id', $tiket->lokasi_id) == $lokasi->id ? 'selected' : '' }}>
                                {{ $lokasi->lokasi }}
                            </option>
                        @endforeach
                    </select>
                    @error('lokasi_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm text-gray-500 font-medium mb-1">Open Tiket</label>
                    <input type="datetime-local" name="open_tiket" value="{{ old('open_tiket', date('Y-m-d\TH:i', strtotime($tiket->open_tiket))) }}" class="w-full border @error('open_tiket') border-red-500 @else border-gray-300 @enderror rounded-md text-sm" required>
                </div>

                <div>
                    <label class="block text-sm text-gray-500 font-medium mb-1">Link Up</label>
                    <input type="datetime-local" name="link_up" value="{{ old('link_up', $tiket->link_up ? date('Y-m-d\TH:i', strtotime($tiket->link_up)) : '') }}" class="w-full border border-gray-300 rounded-md text-sm">
                </div>

                <div>
                    <label class="block text-sm text-gray-500 font-medium mb-1">Durasi</label>
                    <input type="text" name="durasi" value="{{ $tiket->durasi }}" readonly class="w-full bg-gray-100 border border-gray-300 rounded-md text-sm">
                </div>

                <div>
                    <label class="block text-sm text-gray-500 font-medium mb-1">Ganti / Tambah Gambar</label>
                    <input type="file" name="action_images[]" accept="image/*" multiple class="w-full border border-gray-300 rounded-md text-sm">
                    <p class="text-xs text-gray-500">Biarkan kosong jika tidak ingin mengubah gambar lama.</p>
                </div>

@php $images = json_decode($tiket->action_images, true) ?? []; @endphp
@if (count($images))
<div>
    <div class="flex flex-wrap gap-3 mt-2 min-h-[6rem] border border-dashed border-gray-300 p-3 rounded" id="image-preview-wrapper">
        @foreach ($images as $img)
            <div class="relative group">
                <img 
                    src="{{ asset('storage/' . $img) }}" 
                    class="h-24 w-32 object-cover rounded border cursor-pointer transition-transform duration-200 hover:scale-110"
                    onclick="zoomImage('{{ asset('storage/' . $img) }}')"
                >
                <button 
                    type="button"
                    class="absolute top-[-8px] right-[-8px] bg-red-600 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center hover:bg-red-700 z-10"
                    onclick="removeImage(this, '{{ $img }}')"
                >✕</button>
            </div>
        @endforeach
    </div>

    {{-- Preview gambar baru --}}
    <div id="new-image-preview" class="flex flex-wrap gap-3 mt-4"></div>
</div>
@else
{{-- Preview gambar baru tetap tampil --}}
<div id="new-image-preview" class="flex flex-wrap gap-3 mt-4"></div>
@endif

            </div>

            {{-- KANAN --}}
            <div class="space-y-5">
                <div>
                    <label class="block text-sm text-gray-500 font-medium mb-1">Penyebab <span class="text-red-500">*</span></label>
                    <input type="text" name="penyebab" value="{{ old('penyebab', $tiket->penyebab) }}"
                        class="w-full border @error('penyebab') border-red-500 @else border-gray-300 @enderror rounded-md text-sm"
                        required>
                </div>

                <div>
                    <label class="block text-sm text-gray-500 font-medium mb-1">Action <span class="text-red-500">*</span></label>
                    <textarea name="action" rows="2"
                        class="w-full border @error('action') border-red-500 @else border-gray-300 @enderror rounded-md text-sm"
                        required>{{ old('action', $tiket->action) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm text-gray-500 font-medium mb-1">Jenis Gangguan</label>
                    <select name="jenis_gangguan" class="w-full border border-gray-300 rounded-md text-sm" required>
                        <option value="SCADA" {{ old('jenis_gangguan', $tiket->jenis_gangguan) == 'SCADA' ? 'selected' : '' }}>Jaringan SCADA</option>
                        <option value="WAN" {{ old('jenis_gangguan', $tiket->jenis_gangguan) == 'WAN' ? 'selected' : '' }}>WAN Office</option>
                    </select>
                </div>

                <div>
                    <label for="status_koneksi" class="block text-sm text-gray-500 font-medium mb-1">Status Koneksi</label>
                    <div class="flex items-center gap-3">
                        <select name="status_koneksi" id="status_koneksi" class="w-full border border-gray-300 rounded-md text-sm" required>
                            <option value="">-- Pilih Status Koneksi --</option>
                            <option value="Terhubung" {{ old('status_koneksi', $tiket->status_koneksi) == 'Terhubung' ? 'selected' : '' }}>Terhubung</option>
                            <option value="Terkendala" {{ old('status_koneksi', $tiket->status_koneksi) == 'Terkendala' ? 'selected' : '' }}>Terkendala</option>
                            <option value="Putus" {{ old('status_koneksi', $tiket->status_koneksi) == 'Putus' ? 'selected' : '' }}>Putus</option>
                        </select>
                        <span id="status_indicator" class="w-3 h-3 rounded-full bg-gray-300 border border-gray-400"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-gray-500 font-medium mb-1">Status Tiket</label>
                    <select name="status_tiket" id="status_tiket" class="w-full border border-gray-300 rounded-md text-sm" required>
                        <option value="Proses" {{ old('status_tiket', $tiket->status_tiket) == 'Proses' ? 'selected' : '' }}>Proses</option>
                        <option value="Selesai" {{ old('status_tiket', $tiket->status_tiket) == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <div id="pesan_status" class="hidden p-3 text-sm text-yellow-800 bg-yellow-50 border border-yellow-200 rounded">
                    Tim masih proses backup GSM & Recovery FO belum bisa dilakukan karena menunggu progress tim PLN selesai
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">⏱️ Rincian Stopclock</h3>
<div id="stopclock-container" class="space-y-4">
    @foreach ($tiket->stopclocks as $sc)
        @php
            $start = \Carbon\Carbon::parse($sc->start_clock);
            $stop = \Carbon\Carbon::parse($sc->stop_clock);
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 relative group"> {{-- 🔄 Tambah class untuk posisi tombol --}}
            <input type="datetime-local" name="stopclocks[start_clock][]" value="{{ $start->format('Y-m-d\TH:i') }}" class="form-input text-sm" required>
            <input type="datetime-local" name="stopclocks[stop_clock][]" value="{{ $stop->format('Y-m-d\TH:i') }}" class="form-input text-sm" required>
            <input type="text" name="stopclocks[alasan][]" value="{{ $sc->alasan }}" class="form-input text-sm" placeholder="Alasan stopclock" required>

            {{-- 🔄 Tombol hapus --}}
            <button type="button"
                class="absolute -top-2 -right-2 bg-red-600 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center hover:bg-red-700"
                onclick="hapusStopclockRow(this)">✕</button>
        </div>
    @endforeach
</div>
                    <button type="button" id="add-stopclock" class="mt-2 text-sm text-blue-600 hover:underline">+ Tambah Stopclock</button>
                </div>
            </div>
        </div>

        <div class="pt-8 flex justify-between">
            <a href="{{ route('inputtiket.index') }}" class="bg-sky-700 hover:bg-sky-400 text-white text-sm font-medium px-4 py-2 rounded-md shadow">Kembali</a>
            <button id="btn-submit" type="submit" class="bg-sky-700 hover:bg-sky-400 text-white text-sm font-medium px-4 py-2 rounded-md shadow inline-flex items-center gap-2">
                <span id="btn-text">Simpan Perubahan</span>
                <svg id="btn-spinner" class="hidden animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Status Tiket Toggle Pesan
    const statusSelect = document.getElementById('status_tiket');
    const pesanBox = document.getElementById('pesan_status');
    if (statusSelect.value === 'Proses') pesanBox.classList.remove('hidden');

    statusSelect.addEventListener('change', function () {
        this.value === 'Proses' ? pesanBox.classList.remove('hidden') : pesanBox.classList.add('hidden');
    });

    // Preview Gambar Baru (PERBAIKAN)
    const inputImages = document.querySelector('input[name="action_images[]"]');
    const newImagePreview = document.getElementById('new-image-preview');
    const dt = new DataTransfer(); // Buffer file

    inputImages.addEventListener('change', function () {
        dt.items.clear(); // Reset buffer
        newImagePreview.innerHTML = '';

        Array.from(this.files).forEach((file, index) => {
            dt.items.add(file);

            const reader = new FileReader();
            reader.onload = function (e) {
                const div = document.createElement('div');
                div.className = "relative group";
                div.innerHTML = `
                    <img src="${e.target.result}" 
                        class="h-24 w-32 object-cover rounded border cursor-pointer transition-transform duration-200 hover:scale-110"
                        onclick="zoomImage('${e.target.result}')">
                    <button type="button"
                        class="absolute top-[-8px] right-[-8px] bg-red-600 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center hover:bg-red-700 z-10"
                        onclick="removeNewImage(this)">✕</button>
                `;
                newImagePreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });

        this.files = dt.files;
    });

    // Pasang Listener pada stopclock awal
    pasangListenerStopclock();

    // Recalculate on open/link change
    document.querySelector('input[name="open_tiket"]').addEventListener('change', hitungDurasiFinal);
    document.querySelector('input[name="link_up"]').addEventListener('change', hitungDurasiFinal);

    // Update indikator koneksi
    updateStatusIndicator();
    document.getElementById('status_koneksi').addEventListener('change', updateStatusIndicator);
});

// Fungsi: Hapus gambar baru (PERBAIKAN)
function removeNewImage(button) {
    const imageDiv = button.closest('.group');
    const index = Array.from(imageDiv.parentNode.children).indexOf(imageDiv);

    const input = document.querySelector('input[name="action_images[]"]');
    const dt = new DataTransfer();
    const { files } = input;

    Array.from(files).forEach((file, i) => {
        if (i !== index) dt.items.add(file);
    });

    input.files = dt.files;
    input.dispatchEvent(new Event('change'));
}

// Fungsi: Hapus gambar lama
function removeImage(button, path) {
    const deletedInput = document.getElementById('deleted_images');
    let paths = deletedInput.value ? deletedInput.value.split(',') : [];
    paths.push(path);
    deletedInput.value = paths.join(',');

    const wrapper = button.closest('.group');
    wrapper.remove();
}

// Fungsi: Zoom gambar
function zoomImage(src) {
    const modal = document.createElement('div');
    modal.className = "fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50";
    modal.innerHTML = `
        <div class="relative max-w-[90%] max-h-[90%]">
            <img src="${src}" class="max-w-full max-h-full rounded shadow-xl">
            <button class="absolute top-[-10px] right-[-10px] text-white bg-red-600 hover:bg-red-700 rounded-full px-3 py-1 text-sm" onclick="this.parentElement.parentElement.remove()">✕</button>
        </div>
    `;
    document.body.appendChild(modal);
}

// Fungsi: Hapus baris stopclock
function hapusStopclockRow(button) {
    const row = button.closest('.grid');
    if (row) row.remove();
    hitungDurasiFinal();
}

// Fungsi: Tambah stopclock baru
document.getElementById('add-stopclock')?.addEventListener('click', () => {
    const container = document.getElementById('stopclock-container');
    const row = document.createElement('div');
    row.className = 'grid grid-cols-1 md:grid-cols-3 gap-2 relative group';
    row.innerHTML = `
        <input type="datetime-local" name="stopclocks[start_clock][]" class="form-input text-sm" required>
        <input type="datetime-local" name="stopclocks[stop_clock][]" class="form-input text-sm" required>
        <input type="text" name="stopclocks[alasan][]" class="form-input text-sm" placeholder="Alasan" required>
        <button type="button"
            class="absolute -top-2 -right-2 bg-red-600 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center hover:bg-red-700"
            onclick="hapusStopclockRow(this)">✕</button>
    `;
    container.appendChild(row);

    pasangListenerStopclock();
    hitungDurasiFinal();
});

// Fungsi: Pasang listener pada input stopclock
function pasangListenerStopclock() {
    const startInputs = document.querySelectorAll('input[name="stopclocks[start_clock][]"]');
    const stopInputs = document.querySelectorAll('input[name="stopclocks[stop_clock][]"]');

    startInputs.forEach(input => {
        input.removeEventListener('change', hitungDurasiFinal);
        input.addEventListener('change', hitungDurasiFinal);
    });

    stopInputs.forEach(input => {
        input.removeEventListener('change', hitungDurasiFinal);
        input.addEventListener('change', hitungDurasiFinal);
    });
}

// Fungsi: Hitung durasi akhir
function hitungDurasiFinal() {
    const openInput = document.querySelector('input[name="open_tiket"]');
    const linkInput = document.querySelector('input[name="link_up"]');
    const durasiInput = document.querySelector('input[name="durasi"]');

    const openTime = new Date(openInput.value);
    const linkTime = new Date(linkInput.value);

    if (isNaN(openTime) || isNaN(linkTime)) {
        durasiInput.value = '{{ $tiket->durasi }}';
        return;
    }

    let totalDurasi = (linkTime - openTime) / 60000; // dalam menit

    let totalStopclock = 0;
    const stopclockRows = document.querySelectorAll('#stopclock-container > .grid');

    stopclockRows.forEach(row => {
        const start = row.querySelector('input[name="stopclocks[start_clock][]"]');
        const stop = row.querySelector('input[name="stopclocks[stop_clock][]"]');

        if (start?.value && stop?.value) {
            const startTime = new Date(start.value);
            const stopTime = new Date(stop.value);
            let dur = (stopTime - startTime) / 60000;
            if (!isNaN(dur) && dur > 0) totalStopclock += dur;
        }
    });

    let durasiBersih = totalDurasi - totalStopclock;
    if (durasiBersih < 0) durasiBersih = 0;

    const jam = Math.floor(durasiBersih / 60);
    const menit = Math.floor(durasiBersih % 60);
    durasiInput.value = (jam > 0 ? `${jam} Jam ` : '') + `${menit} Menit`;
}

// Fungsi: Saat submit form
document.querySelector('form').addEventListener('submit', function () {
    const btn = document.getElementById('btn-submit');
    const text = document.getElementById('btn-text');
    const spinner = document.getElementById('btn-spinner');

    btn.disabled = true;
    text.textContent = 'Menyimpan...';
    spinner.classList.remove('hidden');
});

// Fungsi: Update indikator status koneksi
function updateStatusIndicator() {
    const select = document.getElementById('status_koneksi');
    const indicator = document.getElementById('status_indicator');

    switch (select.value) {
        case 'Terhubung':
            indicator.className = 'w-3 h-3 rounded-full bg-green-500';
            break;
        case 'Terkendala':
            indicator.className = 'w-3 h-3 rounded-full bg-yellow-400';
            break;
        case 'Putus':
            indicator.className = 'w-3 h-3 rounded-full bg-red-500';
            break;
        default:
            indicator.className = 'w-3 h-3 rounded-full bg-gray-300 border border-gray-400';
    }
}
</script>

@endsection
