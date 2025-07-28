<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property bool $can_manage_users
 * @property bool $can_manage_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AkunPengguna newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AkunPengguna newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AkunPengguna query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AkunPengguna whereCanManageData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AkunPengguna whereCanManageUsers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AkunPengguna whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AkunPengguna whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AkunPengguna whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AkunPengguna whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AkunPengguna wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AkunPengguna whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AkunPengguna whereUpdatedAt($value)
 */
	class AkunPengguna extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $no_tiket
 * @property int $lokasi_id
 * @property string|null $open_tiket
 * @property string|null $link_up
 * @property string|null $durasi
 * @property string|null $penyebab
 * @property string|null $action
 * @property string|null $action_images
 * @property string|null $status_koneksi
 * @property string|null $status_tiket
 * @property string|null $jenis_gangguan
 * @property-read \Illuminate\Support\Collection $stopclocks
 * @property-read \App\Models\Lokasi|null $lokasi
 * @property string|null $stopclock
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $action_images_array
 * @property-read mixed $formatted_durasi
 * @property-read mixed $link_up_formatted
 * @property-read mixed $open_tiket_formatted
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TiketLog> $logs
 * @property-read int|null $logs_count
 * @property-read int|null $stopclocks_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputTiket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputTiket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputTiket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputTiket whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputTiket whereActionImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputTiket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputTiket whereDurasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputTiket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputTiket whereJenisGangguan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputTiket whereLinkUp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputTiket whereLokasiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputTiket whereNoTiket($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputTiket whereOpenTiket($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputTiket wherePenyebab($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputTiket whereStatusKoneksi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputTiket whereStatusTiket($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputTiket whereStopclock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputTiket whereUpdatedAt($value)
 */
	class InputTiket extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $judul
 * @property string $tanggal
 * @property string $kegiatan
 * @property string|null $apa
 * @property string|null $siapa
 * @property string|null $kapan
 * @property string|null $dimana
 * @property string|null $mengapa
 * @property string|null $bagaimana
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKegiatan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKegiatan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKegiatan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKegiatan whereApa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKegiatan whereBagaimana($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKegiatan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKegiatan whereDimana($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKegiatan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKegiatan whereJudul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKegiatan whereKapan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKegiatan whereKegiatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKegiatan whereMengapa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKegiatan whereSiapa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKegiatan whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKegiatan whereUpdatedAt($value)
 */
	class LaporanKegiatan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $no
 * @property string $lokasi
 * @property string $sid
 * @property string|null $product
 * @property string|null $bandwith
 * @property string|null $kategori_layanan
 * @property int $jumlah_gangguan
 * @property float $standard_availability
 * @property float $realisasi_availability
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InputTiket> $inputTikets
 * @property-read int|null $input_tikets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereBandwith($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereJumlahGangguan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereKategoriLayanan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereLokasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereRealisasiAvailability($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereStandardAvailability($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereUpdatedAt($value)
 */
	class Lokasi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $input_tiket_id
 * @property string $start_clock
 * @property string|null $stop_clock
 * @property string|null $alasan
 * @property int $durasi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputTiket $tiket
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stopclock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stopclock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stopclock query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stopclock whereAlasan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stopclock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stopclock whereDurasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stopclock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stopclock whereInputTiketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stopclock whereStartClock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stopclock whereStopClock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stopclock whereUpdatedAt($value)
 */
	class Stopclock extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $input_tiket_id
 * @property string $status
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $log_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputTiket $tiket
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog whereInputTiketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog whereLogTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TiketLog whereUpdatedAt($value)
 */
	class TiketLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

