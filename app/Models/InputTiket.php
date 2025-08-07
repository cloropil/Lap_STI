<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * @property int $id
 * @property string $no_tiket
 * @property int $lokasi_id
 * @property string|null $open_tiket
 * @property string|null $link_up
 * @property string|null $link_upGSM  
 * @property string|null $durasi
 * @property string|null $penyebab
 * @property string|null $action
 * @property string|null $action_images
 * @property string|null $status_koneksi
 * @property string|null $status_tiket
 * @property string|null $jenis_gangguan
 * * @property-read \Illuminate\Support\Collection $stopclocks
 * @property-read \App\Models\Lokasi|null $lokasi
 */
class InputTiket extends Model
{
    use HasFactory;

    protected $fillable = [
        'lokasi_id',
        'no_tiket',
        'open_tiket',
        'stopclock',
        'link_up',
        'link_upGSM', // Tambahkan 'link_upGSM'
        'durasi',
        'penyebab',
        'action',
        'action_images',
        'status_koneksi',
        'status_tiket',
        'jenis_gangguan',
    ];

    // Relasi ke tabel lokasi
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }

    // Relasi ke logs
    public function logs()
    {
        return $this->hasMany(TiketLog::class);
    }

    // Relasi ke stopclock
    public function stopclocks()
    {
        return $this->hasMany(Stopclock::class);
    }

    // Generate nomor tiket unik
    public static function generateTicketNumber()
    {
        do {
            $noTiket = 'RENK' . strtoupper(Str::random(4));
        } while (self::where('no_tiket', $noTiket)->exists());

        return $noTiket;
    }

    // Accessor untuk action_images dalam bentuk array
    public function getActionImagesArrayAttribute()
    {
        $decoded = json_decode($this->action_images ?? '[]', true);
        return is_array($decoded) ? $decoded : [];
    }

    // Accessor untuk format tanggal open_tiket
    public function getOpenTiketFormattedAttribute()
    {
        return $this->open_tiket ? Carbon::parse($this->open_tiket)->format('d M Y H:i') : '-';
    }

    // Accessor untuk format tanggal link_up (FO)
    public function getLinkUpFormattedAttribute()
    {
        return $this->link_up ? Carbon::parse($this->link_up)->format('d M Y H:i') : '-';
    }
    
    // Accessor untuk format tanggal link_upGSM
    public function getLinkUpGSMFormattedAttribute()
    {
        return $this->link_upGSM ? Carbon::parse($this->link_upGSM)->format('d M Y H:i') : '-';
    }

    // Accessor fallback durasi
    public function getFormattedDurasiAttribute()
    {
        return $this->durasi ?? 'Belum dihitung';
    }
}