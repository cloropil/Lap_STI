<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanKegiatan extends Model
{
    protected $table = 'laporan_kegiatan'; // opsional, tapi dianjurkan untuk memperjelas

    protected $fillable = [
        'tanggal',
        'kegiatan',
        'judul',
        'apa',
        'siapa',
        'kapan',
        'dimana',
        'mengapa',
        'bagaimana',
    ];
}
