<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TiketLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'input_tiket_id',
        'status',
        'keterangan',
        'log_time',
    ];

    protected $casts = [
        'log_time' => 'datetime',
    ];

    public function tiket()
    {
        return $this->belongsTo(InputTiket::class, 'input_tiket_id');
    }
}
