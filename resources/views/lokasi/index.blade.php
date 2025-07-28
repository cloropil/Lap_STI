@extends('layouts.app')

@section('title', 'Daftar Lokasi')

@section('content')

@php
    /** @var \App\Models\AkunPengguna $user */
    $user = auth()->user();
@endphp

<div class="container mx-auto px-4 py-12 fade-slide">

    {{-- MODAL ALERT SUKSES --}}
    @if(session('success'))
        <div id="modalSuccess" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30" onclick="closeSuccessModal()">
            <div class="bg-white rounded-md p-6 shadow-lg text-center max-w-sm w-full mx-4" onclick="event.stopPropagation()">
                <h3 class="text-lg font-semibold text-green-700 mb-2">Berhasil</h3>
                <p class="text-sm text-gray-700">{{ session('success') }}</p>
                <button onclick="closeSuccessModal()"
                        class="mt-4 px-4 py-2 text-sm text-white bg-sky-600 hover:bg-sky-400 rounded-md">
                    Tutup
                </button>
            </div>
        </div>
    @endif

    {{-- Header dan Tombol --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-dark mb-4 md:mb-0">Daftar Lokasi</h2>

        @if(in_array($user->role, ['superadmin', 'admin']))
            <a href="{{ route('lokasi.create') }}"
               class="bg-sky-700 hover:bg-sky-400 text-white text-sm font-medium px-4 py-2 rounded-md shadow inline-flex items-center gap-2 transition-all duration-200 ease-in-out">
                Input Lokasi
            </a>
        @elseif($user->role === 'staff')
            <a href="#"
               onclick="showAccessModal(event, 'menambah lokasi')"
               class="bg-sky-700 hover:bg-sky-400 text-white text-sm font-medium px-4 py-2 rounded-md shadow inline-flex items-center gap-2 transition-all duration-200 ease-in-out">
                Input Lokasi
            </a>
        @endif
    </div>

    {{-- Tabel Lokasi --}}
    <div class="overflow-x-auto rounded-lg shadow">
        <table class="min-w-full border border-gray-400 text-sm text-gray-800 bg-white">
            <thead class="bg-sky-700 text-white text-center">
                <tr>
                    <th class="border border-gray-400 px-3 py-2 w-12">#</th>
                    <th class="border border-gray-400 px-3 py-2">Nama Lokasi</th>
                    <th class="border border-gray-400 px-3 py-2">SID</th>
                    <th class="border border-gray-400 px-3 py-2">Produk</th>
                    <th class="border border-gray-400 px-3 py-2">Bandwith</th>
                    <th class="border border-gray-400 px-3 py-2">Kategori</th>
                    <th class="border border-gray-400 px-3 py-2">Std Avail (%)</th>
                    <th class="border border-gray-400 px-3 py-2">Realisasi (%)</th>
                    <th class="border border-gray-400 px-3 py-2 w-36">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @forelse ($lokasis as $lokasi)
                    <tr class="hover:bg-gray-100">
                        <td class="border border-gray-400 px-3 py-2">{{ $lokasi->no }}</td>
                        <td class="border border-gray-400 px-3 py-2 text-left">{{ $lokasi->lokasi }}</td>
                        <td class="border border-gray-400 px-3 py-2 text-left">{{ $lokasi->sid }}</td>
                        <td class="border border-gray-400 px-3 py-2 text-left">{{ $lokasi->product ?? '-' }}</td>
                        <td class="border border-gray-400 px-3 py-2 text-left">{{ $lokasi->bandwith ?? '-' }}</td>
                        <td class="border border-gray-400 px-3 py-2 text-left">{{ $lokasi->kategori_layanan ?? '-' }}</td>
                        <td class="border border-gray-400 px-3 py-2 text-end">{{ $lokasi->standard_availability ?? '0.00' }}</td>
                        <td class="border border-gray-400 px-3 py-2 text-end">{{ $lokasi->realisasi_availability ?? '0.00' }}</td>
                        <td class="border border-gray-400 px-3 py-2 whitespace-nowrap space-x-2">

                            @if(in_array($user->role, ['superadmin', 'admin']))
                                <a href="{{ route('lokasi.edit', $lokasi->id) }}"
                                   class="text-orange-500 hover:underline text-sm font-medium">Edit</a>

                                <form action="{{ route('lokasi.destroy', $lokasi->id) }}" method="POST" class="inline" data-id="{{ $lokasi->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="text-red-600 hover:underline text-sm font-medium bg-transparent border-0"
                                            onclick="openDeleteModal({{ $lokasi->id }})">
                                        Hapus
                                    </button>
                                </form>
                            @elseif($user->role === 'staff')
                                <a href="#"
                                   onclick="showAccessModal(event, 'mengedit lokasi')"
                                   class="text-orange-500 hover:underline text-sm font-medium cursor-pointer">Edit</a>

                                <button type="button"
                                        onclick="showAccessModal(event, 'menghapus lokasi')"
                                        class="text-red-600 hover:underline text-sm font-medium bg-transparent border-0 cursor-pointer">
                                    Hapus
                                </button>
                            @endif

                        </td>
                    </tr>
                @empty
                    <tr class="text-center">
                        <td class="border border-gray-400 px-3 py-6 text-gray-500">-</td>
                        <td class="border border-gray-400 px-3 py-6 text-gray-500" colspan="8">
                            <div class="flex flex-col items-center justify-center">
                                <i class="bi bi-geo-alt text-4xl mb-2 text-blue-300"></i>
                                <p class="text-base font-medium">Tidak ada data lokasi</p>
                                <p class="text-sm text-blue-600">Belum ada lokasi yang ditambahkan.</p>
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
        <h3 class="text-lg font-semibold text-red-700 mb-2">Konfirmasi Hapus</h3>
        <p class="text-sm text-gray-700">Yakin ingin menghapus lokasi ini?</p>
        <div class="flex justify-center gap-3 mt-6">
            <button onclick="submitDeleteForm()" class="px-4 py-2 text-sm text-white bg-red-600 hover:bg-red-500 rounded-md">Hapus</button>
            <button onclick="closeDeleteModal()" class="px-4 py-2 text-sm text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-md">Batal</button>
        </div>
    </div>
</div>

{{-- MODAL AKSES DITOLAK --}}
<div id="accessModal" class="fixed inset-0 z-50 hidden items-center justify-center backdrop-blur-sm bg-black/30" onclick="closeAccessModal()">
    <div class="bg-white rounded-md p-6 shadow-lg text-center max-w-sm w-full mx-4" onclick="event.stopPropagation()">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Akses Ditolak</h3>
        <p class="text-sm text-gray-600" id="modalReason">Anda tidak diizinkan melakukan tindakan ini.</p>
        <button onclick="closeAccessModal()"
                class="mt-4 px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-500 rounded-md">
            Tutup
        </button>
    </div>
</div>

{{-- SCRIPT --}}
<script>
    function closeSuccessModal() {
        const modal = document.getElementById('modalSuccess');
        if (modal) modal.classList.add('hidden');
    }

    let deleteId = null;

    function openDeleteModal(id) {
        deleteId = id;
        const modal = document.getElementById('modalDelete');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('modalDelete');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        deleteId = null;
    }

    function submitDeleteForm() {
        if (deleteId !== null) {
            const form = document.querySelector(`form[data-id='${deleteId}']`);
            if (form) form.submit();
        }
    }

    function showAccessModal(event, action = 'tindakan ini') {
        event.preventDefault();
        const modal = document.getElementById('accessModal');
        const reason = document.getElementById('modalReason');
        reason.innerText = `Staff tidak diizinkan untuk ${action}.`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeAccessModal() {
        const modal = document.getElementById('accessModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection
