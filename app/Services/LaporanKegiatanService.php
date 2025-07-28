<?php

namespace App\Services;

use App\Models\LaporanKegiatan;
use Illuminate\Http\Request;

class LaporanKegiatanService
{
    public function store(Request $request): LaporanKegiatan
    {
        $data = $request->only([
            'tanggal', 'kegiatan', 'judul', 'apa', 'siapa',
            'kapan', 'dimana', 'mengapa', 'bagaimana'
        ]);

        return LaporanKegiatan::create($data);
    }

    public function update(Request $request, LaporanKegiatan $laporan): bool
    {
        $data = $request->only([
            'tanggal', 'kegiatan', 'judul', 'apa', 'siapa',
            'kapan', 'dimana', 'mengapa', 'bagaimana'
        ]);

        return $laporan->update($data);
    }

    public function delete(LaporanKegiatan $laporan): bool
    {
        return $laporan->delete();
    }
}
