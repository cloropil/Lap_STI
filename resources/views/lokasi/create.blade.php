@extends('layouts.app')

@section('title', 'Tambah Lokasi')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
    <div class="bg-blue-500 px-6 py-4 text-white text-lg font-semibold flex items-center">
        Tambah Layanan Baru
    </div>

    <div class="px-6 py-6">
        {{-- Error Handling --}}
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-base text-red-700 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('lokasi.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="no" class="block text-base font-medium text-gray-600 mb-1">No Urut</label>
                <input type="number" name="no" id="no" value="{{ old('no', $kodeNo) }}"
                    class="w-full bg-gray-100 border border-gray-300 rounded-md text-base px-2 py-1.5" readonly>
            </div>

            <div>
                <label for="lokasi" class="block text-base font-medium text-gray-600 mb-1">Nama Lokasi</label>
                <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi') }}"
                    class="w-full border border-gray-300 rounded-md text-base px-2 py-1.5" required>
            </div>

            <div>
                <label for="sid" class="block text-base font-medium text-gray-600 mb-1">SID</label>
                <input type="text" name="sid" id="sid" value="{{ old('sid') }}"
                    class="w-full border border-gray-300 rounded-md text-base px-2 py-1.5" required>
            </div>

            <div>
                <label for="product" class="block text-base font-medium text-gray-600 mb-1">Kategori Layanan</label>
                <select name="product" id="product"
                    class="w-full border border-gray-300 rounded-md text-base px-2 py-1.5" required>
                    <option value="">-- Pilih --</option>
                    <option value="SCADA" {{ old('product') == 'SCADA' ? 'selected' : '' }}>SCADA</option>
                    <option value="WAN Office" {{ old('product') == 'WAN Office' ? 'selected' : '' }}>WAN Office</option>
                </select>
            </div>

            <div>
                <label for="bandwith" class="block text-base font-medium text-gray-600 mb-1">Bandwidth</label>
                <input type="text" name="bandwith" id="bandwith" value="{{ old('bandwith') }}"
                    class="w-full border border-gray-300 rounded-md text-base px-2 py-1.5">
            </div>

            <div>
                <label for="kategori_layanan" class="block text-base font-medium text-gray-600 mb-1">Produk</label>
                <select name="kategori_layanan" id="kategori_layanan"
                    class="w-full border border-gray-300 rounded-md text-base px-2 py-1.5">
                    <option value="">-- Pilih --</option>
                    <option value="INTERNET" {{ old('kategori_layanan') == 'INTERNET' ? 'selected' : '' }}>INTERNET</option>
                    <option value="IPVPN" {{ old('kategori_layanan') == 'IPVPN' ? 'selected' : '' }}>IPVPN</option>
                    <option value="IPVSAT" {{ old('kategori_layanan') == 'IPVSAT' ? 'selected' : '' }}>IPVSAT</option>
                    <option value="CLEAR CHANNEL" {{ old('kategori_layanan') == 'CLEARCHANNEL' ? 'selected' : '' }}>CLEAR CHANNEL</option>
                    <option value="METRONET" {{ old('kategori_layanan') == 'METRONET' ? 'selected' : '' }}>METRONET</option>

                </select>
            </div>

            <div>
                <label for="standard_availability" class="block text-base font-medium text-gray-600 mb-1">Standard Availability (%)</label>
                <input type="number" step="0.01" name="standard_availability" id="standard_availability"
                    value="{{ old('standard_availability', 99.5) }}"
                    class="w-full border border-gray-300 rounded-md text-base px-2 py-1.5">
            </div>

            <div>
                <label for="realisasi_availability" class="block text-base font-medium text-gray-600 mb-1">Realisasi Availability (%)</label>
                <input type="number" step="0.01" name="realisasi_availability" id="realisasi_availability"
                    value="{{ old('realisasi_availability', 100) }}"
                    class="w-full border border-gray-300 rounded-md text-base px-2 py-1.5">
            </div>

            <div class="pt-4 flex justify-between">
                <a href="{{ route('lokasi.index') }}" class="text-base text-gray-600 hover:underline">Kembali</a>
                <button type="submit"
                    class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-base">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
