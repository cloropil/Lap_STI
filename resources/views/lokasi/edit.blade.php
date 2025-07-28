@extends('layouts.app')

@section('title', 'Edit Lokasi')

@section('content')
<div class="card shadow-sm mx-auto" style="max-width: 600px;">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Edit Lokasi</h5>
    </div>

    <div class="card-body">
        {{-- Error Message --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('lokasi.update', $lokasi->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="no" class="form-label">No Urut</label>
                <input type="text" name="no" id="no" value="{{ $lokasi->no }}" class="form-control bg-light" readonly>
            </div>

            <div class="mb-3">
                <label for="lokasi" class="form-label">Nama Lokasi</label>
                <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi', $lokasi->lokasi) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="sid" class="form-label">SID</label>
                <input type="text" name="sid" id="sid" value="{{ old('sid', $lokasi->sid) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="product" class="form-label">Produk</label>
                <input type="text" name="product" id="product" value="{{ old('product', $lokasi->product) }}" class="form-control">
            </div>

            <div class="mb-3">
                <label for="bandwith" class="form-label">Bandwith</label>
                <input type="text" name="bandwith" id="bandwith" value="{{ old('bandwith', $lokasi->bandwith) }}" class="form-control">
            </div>

            <div class="mb-3">
                <label for="kategori_layanan" class="form-label">Kategori Layanan</label>
                <select name="kategori_layanan" id="kategori_layanan" class="form-select">
                    <option value="">-- Pilih --</option>
                    <option value="Dedicated" {{ old('kategori_layanan', $lokasi->kategori_layanan) == 'Dedicated' ? 'selected' : '' }}>Dedicated</option>
                    <option value="Broadband" {{ old('kategori_layanan', $lokasi->kategori_layanan) == 'Broadband' ? 'selected' : '' }}>Broadband</option>
                    <option value="VPN" {{ old('kategori_layanan', $lokasi->kategori_layanan) == 'VPN' ? 'selected' : '' }}>VPN</option>
                    <option value="IP Transit" {{ old('kategori_layanan', $lokasi->kategori_layanan) == 'IP Transit' ? 'selected' : '' }}>IP Transit</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="standard_availability" class="form-label">Standard Availability (%)</label>
                <input type="number" step="0.01" name="standard_availability" id="standard_availability"
                       value="{{ old('standard_availability', $lokasi->standard_availability) }}" class="form-control">
            </div>

            <div class="mb-3">
                <label for="realisasi_availability" class="form-label">Realisasi Availability (%)</label>
                <input type="number" step="0.01" name="realisasi_availability" id="realisasi_availability"
                       value="{{ old('realisasi_availability', $lokasi->realisasi_availability) }}" class="form-control">
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('lokasi.index') }}" class="bg-sky-700 hover:bg-sky-400 text-white text-sm font-medium px-4 py-2 rounded-md shadow inline-flex items-center gap-2 transition-all duration-200 ease-in-out">
                    Kembali
                </a>
                <button type="submit" class="bg-sky-700 hover:bg-sky-400 text-white text-sm font-medium px-4 py-2 rounded-md shadow inline-flex items-center gap-2 transition-all duration-200 ease-in-out">
                    Perbarui
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
