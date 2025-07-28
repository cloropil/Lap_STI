<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $fillable = [
        'no',
        'lokasi',
        'sid',
        'product',
        'bandwith',
        'kategori_layanan',
        'jumlah_gangguan',
        'standard_availability',
        'realisasi_availability',
    ];

    public function inputTikets()
    {
        return $this->hasMany(InputTiket::class);
    }
}
