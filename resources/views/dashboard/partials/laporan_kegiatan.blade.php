<!-- Judul dengan garis pemisah -->
<div class="mb-4 border-b border-gray-300 pb-2">
    <h2 class="text-lg sm:text-xl font-semibold text-gray-800">📋 Daftar Laporan Kegiatan</h2>
</div>

<!-- Filter Form: kiri, kecil dan rapi -->
<div class="flex flex-col sm:flex-row sm:justify-start sm:items-center gap-2 mb-4">
    <input type="date" id="filterTanggal"
        class="border border-gray-300 rounded px-2 py-1 text-xs w-full sm:w-40"
        value="{{ $selectedDate ?? '' }}">

    <input type="text" id="filterSearch"
        class="border border-gray-300 rounded px-2 py-1 text-xs w-full sm:w-56"
        placeholder="Cari judul/deskripsi"
        value="{{ $keyword ?? '' }}">

    <button type="button" onclick="cariLaporanKegiatan()"
        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs transition">
        Cari
    </button>
</div>

<!-- Tabel Data -->
<div class="overflow-x-auto">
    <table class="w-full text-sm bg-white border border-gray-200 rounded shadow-sm">
        <thead>
            <tr class="bg-gray-100 text-gray-700 text-left">
                <th class="px-4 py-2 border-b">Tanggal</th>
                <th class="px-4 py-2 border-b">Judul</th>
                <th class="px-4 py-2 border-b">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporans as $laporan)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border-b whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($laporan->tanggal)->format('d M Y') }}
                    </td>
                    <td class="px-4 py-2 border-b">{{ $laporan->judul }}</td>
                    <td class="px-4 py-2 border-b">
                        {{ \Illuminate\Support\Str::limit($laporan->kegiatan, 40) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center py-3 text-gray-500">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-4">
    {!! $laporans->appends(request()->only('tanggal', 'keyword'))->links('vendor.pagination.tailwind') !!}
</div>
