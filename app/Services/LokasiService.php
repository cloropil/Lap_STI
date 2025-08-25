<?php

namespace App\Services;

use App\Models\Lokasi;

class LokasiService
{
    public function getAll()
    {
        return Lokasi::select(
            'id',
            'no',
            'lokasi',
            'sid',
            'product',
            'bandwith',
            'kategori_layanan',
            'standard_availability',
            'realisasi_availability'
        )->get();
    }

    // Ambil nomor urut berikutnya (otomatis +1 dari terbesar)
    public function getNextNumber()
    {
        $lastNo = Lokasi::max('no');
        return $lastNo ? $lastNo + 1 : 1;
    }

    public function store(array $data)
    {
        return Lokasi::create([
            'no' => $data['no'],
            'lokasi' => $data['lokasi'],
            'sid' => $data['sid'],
            'product' => $data['product'] ?? null,
            'bandwith' => $data['bandwith'] ?? null,
            'kategori_layanan' => $data['kategori_layanan'] ?? null,
            'standard_availability' => $data['standard_availability'] ?? 99.5,
            'realisasi_availability' => $data['realisasi_availability'] ?? 100,
            'jumlah_gangguan' => 0,
        ]);
    }

    public function getById($id)
    {
        return Lokasi::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $lokasi = $this->getById($id);
        return $lokasi->update([
            'lokasi' => $data['lokasi'],
            'sid' => $data['sid'],
            'product' => $data['product'] ?? null,
            'bandwith' => $data['bandwith'] ?? null,
            'kategori_layanan' => $data['kategori_layanan'] ?? null,
            'standard_availability' => $data['standard_availability'] ?? $lokasi->standard_availability,
            'realisasi_availability' => $data['realisasi_availability'] ?? $lokasi->realisasi_availability,
        ]);
    }

    public function delete($id)
    {
        $lokasi = $this->getById($id);
        return $lokasi->delete();
    }
}
