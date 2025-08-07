@extends('layouts.app')

@section('title', 'Data Tiket Gangguan')

@section('content')

@php
    /** @var \App\Models\AkunPengguna $user */
    $user = Auth::user();
    $userRole = $user?->role;
@endphp

<div class="container mx-auto px-4 py-10 fade-slide">

    {{-- MODAL ALERT ERROR --}}
    @if(session('error'))
        <div id="modalError" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30" onclick="closeErrorModal()">
            <div class="bg-white rounded-md p-6 shadow-lg text-center max-w-sm w-full mx-4" onclick="event.stopPropagation()">
                <h3 class="text-lg font-semibold text-sky-700 mb-2">Terjadi Kesalahan</h3>
                <p class="text-sm text-gray-700">{{ session('error') }}</p>
                <button onclick="closeErrorModal()"
                        class="mt-4 px-4 py-2 text-sm text-white bg-sky-600 hover:bg-sky-300 rounded-md">
                    Tutup
                </button>
            </div>
        </div>
    @endif

    {{-- Header dan Filter --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h4 class="text-2xl font-semibold text-black">Daftar Tiket Gangguan</h4>

        <form method="GET" class="flex flex-col sm:flex-row flex-wrap items-start sm:items-center gap-3">
            <input type="text" name="search" placeholder="Cari Tiket..."
                class="border border-gray-300 bg-white text-gray-800 rounded-md px-3 py-2 text-sm w-44 focus:outline-none focus:ring focus:ring-blue-500"
                value="{{ request('search') }}">

            <input type="date" name="tanggal"
                class="border border-gray-300 bg-white text-gray-800 rounded-md px-3 py-2 text-sm w-40 focus:outline-none focus:ring focus:ring-blue-500"
                value="{{ request('tanggal') }}">

            <button type="submit"
                class="bg-sky-700 hover:bg-sky-400 text-white text-sm font-medium px-4 py-2 rounded-md shadow transition-all duration-200">
                Cari
            </button>
            
            {{-- TOMBOL BARU: EKSPOR EXCEL --}}
            <a href="{{ route('inputtiket.export', ['search' => request('search'), 'tanggal' => request('tanggal')]) }}"
               class="bg-green-700 hover:bg-green-500 text-white text-sm font-medium px-4 py-2 rounded-md shadow transition-all duration-200">
                Ekspor Excel
            </a>

            <a href="{{ $userRole === 'staff' ? '#' : route('inputtiket.create') }}"
               onclick="{{ $userRole === 'staff' ? "showAccessDeniedModal(event, 'Anda tidak memiliki izin untuk menambahkan tiket.')" : '' }}"
               class="bg-sky-700 hover:bg-sky-400 text-white text-sm font-medium px-4 py-2 rounded-md shadow transition-all duration-200">
                Tambah Tiket
            </a>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto scrollbar-thin rounded-lg shadow ring-1 ring-gray-100">
        <table class="min-w-full border border-gray-200 text-sm text-gray-800 bg-white">
            <thead class="bg-sky-700 text-white text-center">
                <tr>
                    <th class="border px-3 py-2">#</th>
                    <th class="border px-3 py-2">No Tiket</th>
                    <th class="border px-3 py-2">Lokasi</th>
                    <th class="border px-3 py-2">Gangguan</th>
                    <th class="border px-3 py-2">SID</th>
                    <th class="border px-3 py-2">Open</th>
                    <th class="border px-3 py-2">Link Up FO</th>
                    <th class="border px-3 py-2">Link Up GSM</th>
                    <th class="border px-3 py-2">Durasi</th>
                    <th class="border px-3 py-2">Stopclock</th>
                    <th class="border px-3 py-2">Status</th>
                    <th class="border px-3 py-2">Gambar</th>
                    <th class="border px-3 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @forelse ($tikets as $index => $tiket)
                    <tr class="hover:bg-gray-50 text-center">
                        <td class="border px-3 py-2">{{ $index + 1 }}</td>
                        <td class="border px-3 py-2">{{ $tiket->no_tiket }}</td>
                        <td class="border px-3 py-2 text-left">{{ $tiket->lokasi->lokasi ?? '-' }}</td>
                        <td class="border px-3 py-2 text-left">{{ $tiket->jenis_gangguan ?? '-' }}</td>
                        <td class="border px-3 py-2 text-left">{{ $tiket->lokasi->sid ?? '-' }}</td>
                        <td class="border px-3 py-2">{{ $tiket->open_tiket_formatted }}</td>
                        <td class="border px-3 py-2">{{ $tiket->link_up_formatted }}</td>
                        <td class="border px-3 py-2">{{ $tiket->link_upGSM_formatted }}</td>
                        <td class="border px-3 py-2">{{ $tiket->formatted_durasi }}</td>
                        <td class="border px-3 py-2 text-left">{{ $tiket->stopclock ?? '-' }}</td>
                        <td class="border px-3 py-2">
                            @if($tiket->status_tiket == 'Proses')
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded-full">Open</span>
                            @elseif($tiket->status_tiket == 'Selesai')
                                <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">Closed</span>
                            @else
                                <span class="bg-gray-200 text-gray-800 text-xs font-semibold px-2 py-1 rounded-full">{{ $tiket->status_tiket }}</span>
                            @endif
                        </td>
                        <td class="border px-3 py-2">{{ count($tiket->action_images_array) }}</td>
                        <td class="border px-3 py-2 whitespace-nowrap space-x-2">
                            <a href="{{ route('inputtiket.show', $tiket->id) }}" class="text-blue-600 hover:underline text-sm">Lihat</a>

                            @if ($userRole === 'staff')
                                <a href="#" onclick="showAccessDeniedModal(event, 'Anda tidak memiliki izin untuk mengedit tiket.')" class="text-orange-600 hover:underline text-sm">Edit</a>
                                <button type="button" onclick="showAccessDeniedModal(event, 'Anda tidak memiliki izin untuk menghapus tiket.')" class="text-sky-600 hover:underline text-sm bg-transparent border-0">
                                    Hapus
                                </button>
                            @else
                                <a href="{{ route('inputtiket.edit', $tiket->id) }}" class="text-orange-600 hover:underline text-sm">Update</a>
                                <form action="{{ route('inputtiket.destroy', $tiket->id) }}" method="POST" class="inline" data-form-id="{{ $tiket->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="text-sky-600 hover:underline text-sm bg-transparent border-0"
                                            onclick="openDeleteModal({{ $tiket->id }})">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="bi bi-inbox text-5xl text-gray-300 mb-2"></i>
                                <p class="text-lg font-semibold">Belum ada tiket ditemukan</p>
                                <p class="text-sm text-gray-400">Silakan tambahkan data tiket terlebih dahulu.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL KONFIRMASI HAPUS --}}
<div id="modalDelete" class="fixed inset-0 z-50 hidden items-center justify-center backdrop-blur-sm bg-black/30" onclick="closeDeleteModal()">
    <div class="bg-white rounded-md p-6 shadow-lg text-center max-w-sm w-full mx-4" onclick="event.stopPropagation()">
        <h3 class="text-lg font-semibold text-sky-700 mb-2">Konfirmasi Hapus</h3>
        <p class="text-sm text-gray-700">Yakin ingin menghapus tiket ini?</p>
        <div class="flex justify-center gap-3 mt-6">
            <button onclick="submitDeleteForm()" class="px-4 py-2 text-sm text-white bg-sky-600 hover:bg-sky-300 rounded-md">Hapus</button>
            <button onclick="closeDeleteModal()" class="px-4 py-2 text-sm text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-md">Batal</button>
        </div>
    </div>
</div>

{{-- MODAL AKSES DITOLAK --}}
<div id="accessDeniedModal" class="fixed inset-0 z-50 hidden items-center justify-center backdrop-blur-sm bg-black/30" onclick="closeAccessDeniedModal()">
    <div class="bg-white rounded-md p-6 shadow-lg text-center max-w-sm w-full mx-4" onclick="event.stopPropagation()">
        <h3 class="text-lg font-semibold text-sky-600 mb-2">Akses Ditolak</h3>
        <p id="accessDeniedMessage" class="text-sm text-gray-700">Anda tidak memiliki izin.</p>
        <div class="mt-4">
            <button onclick="closeAccessDeniedModal()" class="px-4 py-2 text-sm text-white bg-sky-600 hover:bg-sky-400 rounded-md">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- SCRIPT MODAL --}}
<script>
    function closeErrorModal() {
        const modal = document.getElementById('modalError');
        if (modal) modal.classList.add('hidden');
    }

    let deleteFormId = null;

    function openDeleteModal(id) {
        deleteFormId = id;
        const modal = document.getElementById('modalDelete');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('modalDelete');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        deleteFormId = null;
    }

    function submitDeleteForm() {
        if (deleteFormId !== null) {
            const form = document.querySelector(`form[data-form-id='${deleteFormId}']`);
            if (form) form.submit();
        }
    }

    function showAccessDeniedModal(event, message = 'Anda tidak memiliki izin untuk melakukan tindakan ini.') {
        event.preventDefault();
        const modal = document.getElementById('accessDeniedModal');
        const messageBox = document.getElementById('accessDeniedMessage');
        if (messageBox) messageBox.textContent = message;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeAccessDeniedModal() {
        const modal = document.getElementById('accessDeniedModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection