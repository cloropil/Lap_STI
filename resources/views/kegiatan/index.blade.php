@extends('layouts.app')

@section('title', 'Laporan Kegiatan Harian')

@section('content')
@php
    /** @var \App\Models\AkunPengguna $user */
    $user = auth()->user();
@endphp

<div class="container mx-auto px-4 py-12 fade-slide">

    {{-- Judul --}}
    <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">
        Daftar Laporan Kegiatan Harian
    </h2>

    {{-- Tombol Tambah --}}
    <div class="flex justify-end mb-3 max-w-4xl mx-auto">
        @if(in_array($user->role, ['superadmin', 'admin']))
            <a href="{{ route('kegiatan.create') }}"
               class="bg-sky-700 hover:bg-sky-400 text-white text-sm font-medium px-4 py-2 rounded-md shadow inline-flex items-center gap-2 transition-all duration-200 ease-in-out">
                Tambah Laporan
            </a>
        @elseif($user->role === 'staff')
            <a href="#"
               onclick="showAccessModal(event, 'menambah')"
               class="bg-sky-700 hover:bg-sky-400 text-white text-sm font-medium px-4 py-2 rounded-md shadow inline-flex items-center gap-2 transition-all duration-200 ease-in-out">
                Tambah Laporan
            </a>
        @endif
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded-md text-sm max-w-4xl mx-auto shadow">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabel --}}
    <div class="overflow-x-auto rounded-md shadow-sm max-w-4xl mx-auto">
        <table class="w-full border border-gray-400 text-sm bg-white">
            <thead class="bg-sky-700 text-white text-center">
                <tr>
                    <th class="border border-gray-400 px-3 py-2 w-10">#</th>
                    <th class="border border-gray-400 px-3 py-2 w-32">Tanggal</th>
                    <th class="border border-gray-400 px-3 py-2 w-48">Judul</th>
                    <th class="border border-gray-400 px-3 py-2">Kegiatan</th>
                    <th class="border border-gray-400 px-3 py-2 w-40">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laporans as $index => $laporan)
                    <tr class="hover:bg-gray-50 transition text-center align-top">
                        <td class="border border-gray-400 px-3 py-2">{{ $index + 1 }}</td>
                        <td class="border border-gray-400 px-3 py-2">
                            {{ \Carbon\Carbon::parse($laporan->tanggal)->format('d-m-Y') }}
                        </td>
                        <td class="border border-gray-400 px-3 py-2 text-left text-gray-700">
                            {{ $laporan->judul ?? '-' }}
                        </td>
                        <td class="border border-gray-400 px-3 py-2 text-left text-gray-700  max-w-xs">
                            {!! nl2br(e(Str::limit($laporan->kegiatan, 120))) !!}
                        </td>
                        <td class="border border-gray-400 px-3 py-2 space-x-2">
                            <a href="{{ route('kegiatan.show', $laporan->id) }}" class="text-blue-600 hover:underline text-sm">
                                Lihat
                            </a>

                            @if(in_array($user->role, ['superadmin', 'admin']))
                                <a href="{{ route('kegiatan.edit', $laporan->id) }}"
                                   class="text-yellow-600 hover:underline text-sm">
                                    Edit
                                </a>
                                <form action="{{ route('kegiatan.destroy', $laporan->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Yakin ingin menghapus laporan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:underline text-sm bg-transparent border-0">
                                        Hapus
                                    </button>
                                </form>
                            @elseif($user->role === 'staff')
                                <a href="#"
                                   onclick="showAccessModal(event, 'mengedit')"
                                   class="text-yellow-600 hover:underline text-sm">
                                    Edit
                                </a>
                                <button type="button"
                                        onclick="showAccessModal(event, 'menghapus')"
                                        class="text-red-600 hover:underline text-sm bg-transparent border-0 cursor-pointer">
                                    Hapus
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr class="text-center">
                        <td class="border border-gray-400 px-3 py-8 text-gray-500" colspan="5">
                            <div class="flex flex-col items-center justify-center">
                                <i class="bi bi-inbox text-5xl mb-4 text-blue-300"></i>
                                <p class="text-lg font-semibold">Tidak ada laporan kegiatan</p>
                                <p class="text-sm text-blue-600">Belum ada laporan kegiatan yang ditambahkan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
    function showAccessModal(event, action = 'tindakan ini') {
        event.preventDefault();
        const modal = document.getElementById('accessModal');
        const reason = document.getElementById('modalReason');
        reason.innerText = `Staff tidak diizinkan untuk ${action} laporan.`;
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
