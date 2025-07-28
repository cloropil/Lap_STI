<?php


namespace App\Models;

use App\Models\InputTiket;

class Laporan
{
    public static function getLaporanHarian($tanggal)
    {
        return InputTiket::with('lokasi')
            ->whereDate('open_tiket', $tanggal)
            ->get();
    }
}
