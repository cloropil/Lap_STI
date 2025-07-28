@extends('layouts.app')

@section('title', 'Edit Laporan Kegiatan Harian')

@section('content')
<div class="max-w-5xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
    <div class="bg-blue-500 px-6 py-4 text-white text-lg font-semibold flex items-center">
        Edit Laporan Kegiatan: {{ $laporan->judul ?? 'Tanpa Judul' }}
    </div>

    <form action="{{ route('kegiatan.update', $laporan->id) }}" method="POST" class="px-6 py-6 space-y-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">
            <div>
                <label for="tanggal" class="block text-sm text-gray-500 font-medium mb-1">Tanggal Kegiatan <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $laporan->tanggal) }}" required
                    class="w-full border @error('tanggal') border-red-500 @else border-gray-300 @enderror rounded-md text-sm px-3 py-2">
                @error('tanggal')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="judul" class="block text-sm text-gray-500 font-medium mb-1">Judul Kegiatan</label>
                <input type="text" name="judul" id="judul" placeholder="Judul kegiatan" value="{{ old('judul', $laporan->judul) }}"
                    class="w-full border @error('judul') border-red-500 @else border-gray-300 @enderror rounded-md text-sm px-3 py-2">
                @error('judul')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="kegiatan" class="block text-sm text-gray-500 font-medium mb-1">Deskripsi Kegiatan <span class="text-red-500">*</span></label>
                <textarea name="kegiatan" id="kegiatan" rows="4" required
                    class="w-full border @error('kegiatan') border-red-500 @else border-gray-300 @enderror rounded-md text-sm px-3 py-2 resize-none">{{ old('kegiatan', $laporan->kegiatan) }}</textarea>
                @error('kegiatan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 5W+1H --}}
            <div>
                <label for="apa" class="block text-sm text-gray-500 font-medium mb-1">Apa</label>
                <textarea name="apa" id="apa" rows="3" placeholder="Apa yang dilakukan?"
                    class="w-full border @error('apa') border-red-500 @else border-gray-300 @enderror rounded-md text-sm px-3 py-2 resize-none">{{ old('apa', $laporan->apa) }}</textarea>
                @error('apa')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="siapa" class="block text-sm text-gray-500 font-medium mb-1">Siapa</label>
                <textarea name="siapa" id="siapa" rows="3" placeholder="Siapa yang terlibat?"
                    class="w-full border @error('siapa') border-red-500 @else border-gray-300 @enderror rounded-md text-sm px-3 py-2 resize-none">{{ old('siapa', $laporan->siapa) }}</textarea>
                @error('siapa')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kapan" class="block text-sm text-gray-500 font-medium mb-1">Kapan</label>
                <textarea name="kapan" id="kapan" rows="3" placeholder="Kapan dilakukan?"
                    class="w-full border @error('kapan') border-red-500 @else border-gray-300 @enderror rounded-md text-sm px-3 py-2 resize-none">{{ old('kapan', $laporan->kapan) }}</textarea>
                @error('kapan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="dimana" class="block text-sm text-gray-500 font-medium mb-1">Di Mana</label>
                <textarea name="dimana" id="dimana" rows="3" placeholder="Lokasi kegiatan"
                    class="w-full border @error('dimana') border-red-500 @else border-gray-300 @enderror rounded-md text-sm px-3 py-2 resize-none">{{ old('dimana', $laporan->dimana) }}</textarea>
                @error('dimana')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="mengapa" class="block text-sm text-gray-500 font-medium mb-1">Mengapa</label>
                <textarea name="mengapa" id="mengapa" rows="3" placeholder="Alasan kegiatan"
                    class="w-full border @error('mengapa') border-red-500 @else border-gray-300 @enderror rounded-md text-sm px-3 py-2 resize-none">{{ old('mengapa', $laporan->mengapa) }}</textarea>
                @error('mengapa')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="bagaimana" class="block text-sm text-gray-500 font-medium mb-1">Bagaimana</label>
                <textarea name="bagaimana" id="bagaimana" rows="3" placeholder="Cara pelaksanaan"
                    class="w-full border @error('bagaimana') border-red-500 @else border-gray-300 @enderror rounded-md text-sm px-3 py-2 resize-none">{{ old('bagaimana', $laporan->bagaimana) }}</textarea>
                @error('bagaimana')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-between pt-6">
            <a href="{{ route('kegiatan.index') }}" class="bg-gray-600 hover:bg-gray-500 text-white text-sm font-medium px-4 py-2 rounded-md shadow transition duration-200">
                Kembali
            </a>

            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium px-4 py-2 rounded-md shadow transition duration-200">
                Update
            </button>
        </div>
    </form>
</div>
@endsection
