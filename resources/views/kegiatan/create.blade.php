@extends('layouts.app')

@section('title', 'Tambah Laporan Kegiatan Harian')

@section('content')
@php
    /** @var \App\Models\AkunPengguna|null $user */
    $user = Auth::user();
    $role = $user?->role ?? 'guest';
@endphp

<div class="max-w-5xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
    <div class="bg-blue-500 px-6 py-4 text-white text-lg font-semibold flex items-center">
        Tambah Laporan Kegiatan Harian
    </div>

    {{-- Error validasi --}}
    @if ($errors->any())
        <div class="m-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded">
            <strong class="block mb-2 font-semibold">Ada kesalahan saat input data:</strong>
            <ul class="list-disc list-inside space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('kegiatan.store') }}" method="POST" enctype="multipart/form-data" class="px-6 py-6 space-y-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">
            {{-- Tanggal dan Judul --}}
            <div>
                <label for="tanggal" class="block text-sm text-gray-500 font-medium mb-1">
                    Tanggal Kegiatan <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal') }}" required
                    class="w-full border @error('tanggal') border-red-500 @else border-gray-300 @enderror rounded-md text-sm px-3 py-2">
                @error('tanggal')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="judul" class="block text-sm text-gray-500 font-medium mb-1">Judul Kegiatan</label>
                <input type="text" name="judul" id="judul" placeholder="Judul kegiatan" value="{{ old('judul') }}"
                    class="w-full border @error('judul') border-red-500 @else border-gray-300 @enderror rounded-md text-sm px-3 py-2">
                @error('judul')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi Kegiatan --}}
            <div class="md:col-span-2">
                <label for="kegiatan" class="block text-sm text-gray-500 font-medium mb-1">
                    Deskripsi Kegiatan <span class="text-red-500">*</span>
                </label>
                <textarea name="kegiatan" id="kegiatan" rows="4" required
                    class="w-full border @error('kegiatan') border-red-500 @else border-gray-300 @enderror rounded-md text-sm px-3 py-2 resize-none">{{ old('kegiatan') }}</textarea>
                @error('kegiatan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 5W+1H --}}
            @php
                $fields = [
                    'apa' => 'Apa yang dilakukan?',
                    'siapa' => 'Siapa yang terlibat?',
                    'kapan' => 'Kapan dilakukan?',
                    'dimana' => 'Lokasi kegiatan',
                    'mengapa' => 'Alasan kegiatan',
                    'bagaimana' => 'Cara pelaksanaan',
                ];
            @endphp

            @foreach ($fields as $field => $placeholder)
                <div>
                    <label for="{{ $field }}" class="block text-sm text-gray-500 font-medium mb-1">
                        {{ ucfirst($field) }}
                    </label>
                    <textarea name="{{ $field }}" id="{{ $field }}" rows="3" placeholder="{{ $placeholder }}"
                        class="w-full border @error($field) border-red-500 @else border-gray-300 @enderror rounded-md text-sm px-3 py-2 resize-none">{{ old($field) }}</textarea>
                    @error($field)
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach
        </div>

        {{-- Dokumentasi dan Tindakan --}}
        <!-- <div class="border-t border-gray-200 pt-8">
            <h3 class="text-gray-700 text-base font-semibold mb-4">📸 Dokumentasi dan Tindakan</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">
                {{-- Tindakan --}}
                <div class="md:col-span-2">
                    <label for="tindakan" class="block text-sm text-gray-500 font-medium mb-1">
                        Tindakan yang Dilakukan
                    </label>
                    <textarea name="tindakan" id="tindakan" rows="4" placeholder="Jelaskan tindakan atau pekerjaan yang dilakukan"
                        class="w-full border @error('tindakan') border-red-500 @else border-gray-300 @enderror rounded-md text-sm px-3 py-2 resize-none">{{ old('tindakan') }}</textarea>
                    @error('tindakan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Upload Gambar --}}
                <div class="md:col-span-2">
                    <label for="foto_kegiatan" class="block text-sm text-gray-500 font-medium mb-1">
                        Upload Gambar Kegiatan
                    </label>
                    <input type="file" name="foto_kegiatan[]" id="foto_kegiatan" multiple
                        class="block w-full text-sm text-gray-700 border border-gray-300 rounded-md cursor-pointer file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0 file:text-sm file:font-semibold
                            file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('foto_kegiatan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div> -->

        {{-- Tombol --}}
        <div class="flex justify-end gap-2 pt-6">
            <a href="{{ route('kegiatan.index') }}"
                class="bg-gray-600 hover:bg-gray-500 text-white text-sm font-medium px-4 py-2 rounded-md shadow transition duration-200">
                Kembali
            </a>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium px-4 py-2 rounded-md shadow transition duration-200">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
