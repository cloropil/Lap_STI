<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Stopclock extends Model
{
    use HasFactory;

    protected $fillable = [
        'input_tiket_id',
        'start_clock',
        'stop_clock',
        'alasan',
        'durasi', // tambahkan kolom durasi agar bisa diisi
    ];

    protected static function boot()
    {
        parent::boot();

        // Hitung durasi secara otomatis saat model dibuat
        static::creating(function ($stopclock) {
            if ($stopclock->start_clock && $stopclock->stop_clock) {
                $start = Carbon::parse($stopclock->start_clock);
                $stop = Carbon::parse($stopclock->stop_clock);
                if ($stop->greaterThan($start)) {
                    $stopclock->durasi = $stop->diffInMinutes($start);
                } else {
                    $stopclock->durasi = 0;
                }
            }
        });

        // Juga hitung ulang jika di-update
        static::updating(function ($stopclock) {
            if ($stopclock->start_clock && $stopclock->stop_clock) {
                $start = Carbon::parse($stopclock->start_clock);
                $stop = Carbon::parse($stopclock->stop_clock);
                if ($stop->greaterThan($start)) {
                    $stopclock->durasi = $stop->diffInMinutes($start);
                } else {
                    $stopclock->durasi = 0;
                }
            }
        });
    }

    public function tiket()
    {
        return $this->belongsTo(InputTiket::class, 'input_tiket_id');
    }
}
