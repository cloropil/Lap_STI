<?php

namespace App\Services;

use App\Models\InputTiket;
use App\Models\LaporanKegiatan;
use Carbon\Carbon;

class LaporanService
{
    public function getTanggal($request)
    {
        return $request->input('tanggal', Carbon::now()->format('Y-m-d'));
    }

    public function getTiketHarian($tanggal)
    {
        return InputTiket::with('lokasi')
            ->whereDate('open_tiket', $tanggal)
            ->get();
    }

    public function getTiketHarianLengkap($tanggal)
    {
        return InputTiket::with(['lokasi', 'stopclocks'])
            ->whereDate('open_tiket', $tanggal)
            ->get();
    }

    public function getKegiatanHarian($tanggal)
    {
        return LaporanKegiatan::whereDate('tanggal', $tanggal)->get();
    }
}
