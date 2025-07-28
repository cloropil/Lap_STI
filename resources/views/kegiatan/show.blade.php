@extends('layouts.app')

@section('title', 'Detail Laporan Kegiatan Harian')

@section('content')
<div class="max-w-5xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
    <div class="bg-blue-500 px-6 py-4 text-white text-lg font-semibold flex items-center">
        Detail Laporan Kegiatan: {{ $laporan->judul ?? 'Tanpa Judul' }}
    </div>

    <div class="px-6 py-6 space-y-8 text-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Kegiatan</label>
                <p class="text-base font-semibold">{{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('l, d F Y') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Judul Kegiatan</label>
                <p class="text-base">{{ $laporan->judul ?? '-' }}</p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">Deskripsi Kegiatan</label>
                <div class="border border-gray-300 rounded-md p-4 bg-gray-50 whitespace-pre-line">
                    {!! nl2br(e($laporan->kegiatan)) !!}
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Apa</label>
                <div class="border border-gray-300 rounded-md p-4 bg-gray-50 whitespace-pre-line">
                    {!! nl2br(e($laporan->apa ?? '-')) !!}
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Siapa</label>
                <div class="border border-gray-300 rounded-md p-4 bg-gray-50 whitespace-pre-line">
                    {!! nl2br(e($laporan->siapa ?? '-')) !!}
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Kapan</label>
                <div class="border border-gray-300 rounded-md p-4 bg-gray-50 whitespace-pre-line">
                    {!! nl2br(e($laporan->kapan ?? '-')) !!}
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Di Mana</label>
                <div class="border border-gray-300 rounded-md p-4 bg-gray-50 whitespace-pre-line">
                    {!! nl2br(e($laporan->dimana ?? '-')) !!}
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Mengapa</label>
                <div class="border border-gray-300 rounded-md p-4 bg-gray-50 whitespace-pre-line">
                    {!! nl2br(e($laporan->mengapa ?? '-')) !!}
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Bagaimana</label>
                <div class="border border-gray-300 rounded-md p-4 bg-gray-50 whitespace-pre-line">
                    {!! nl2br(e($laporan->bagaimana ?? '-')) !!}
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('kegiatan.index') }}"
                class="bg-gray-600 hover:bg-gray-500 text-white text-sm font-medium px-4 py-2 rounded-md shadow transition duration-200">
                Kembali
            </a>
        </div>
    </div>
</div>
@endsection
