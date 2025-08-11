<?php

namespace App\Services;

use App\Models\InputTiket;
use App\Models\LaporanKegiatan;
use App\Models\Stopclock;
use App\Models\TiketLog;
use App\Models\Lokasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class InputTiketService
{
    /**
     * Mengambil tiket yang sudah difilter.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFilteredTikets(Request $request)
    {
        $query = InputTiket::with('lokasi')->latest();

        if ($request->filled('search')) {
            $keyword = $request->input('search');
            $query->where(function ($q) use ($keyword) {
                $q->where('no_tiket', 'like', "%$keyword%")
                    ->orWhere('status_tiket', 'like', "%$keyword%")
                    ->orWhereHas('lokasi', fn($lokasiQuery) => $lokasiQuery->where('lokasi', 'like', "%$keyword%"));
            });
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('open_tiket', $request->input('tanggal'));
        }

        return $query->paginate(6);
    }

    /**
     * Mengambil laporan kegiatan harian.
     *
     * @param Request $request
     * @return array
     */
    public function getLaporanHarian(Request $request)
    {
        $tanggal = $request->input('tangram', now()->format('Y-m-d'));

        return [
            'tikets' => InputTiket::with('lokasi')->whereDate('open_tiket', $tanggal)->get(),
            'kegiatanHarian' => LaporanKegiatan::whereDate('tanggal', $tanggal)->get(),
            'tanggal' => $tanggal,
        ];
    }

    /**
     * Menyimpan data tiket baru.
     *
     * @param Request $request
     * @return InputTiket
     */
    public function storeTiket(Request $request)
    {
        $data = $request->validate([
            'lokasi_id' => 'required|exists:lokasis,id',
            'no_tiket' => 'required|unique:input_tikets,no_tiket',
            'open_tiket' => 'required|date',
            'link_up' => 'nullable|date',
            'link_upGSM' => 'nullable|date',
            'penyebab' => 'nullable|string',
            'action' => 'nullable|string',
            'status_koneksi' => 'nullable|string',
            'jenis_gangguan' => 'nullable|string',
            'status_tiket' => 'nullable|string',
            'action_images.*' => 'image|max:2048',
        ]);

        $imagePaths = collect($request->file('action_images', []))
            ->map(fn($file) => $file->store('action_images', 'public'))
            ->toArray();

        $statusTiket = $request->filled('link_up') ? 'Selesai' : $request->input('status_tiket', 'Proses');

        $tiket = InputTiket::create(array_merge($data, [
            'status_tiket' => $statusTiket,
            'action_images' => json_encode($imagePaths),
            'stopclock' => '0x stopclock',
        ]));

        $totalStopclock = $this->storeStopclocks($request, $tiket->id);

        // Panggil updateDurasi untuk menghitung durasi FO dan GSM
        $this->updateDurasi($tiket, $totalStopclock);

        $this->createTiketLog($tiket->id, $statusTiket, Auth::user()->email);

        return $tiket;
    }

    /**
     * Memperbarui data tiket yang sudah ada.
     *
     * @param Request $request
     * @param int $id
     * @return InputTiket
     */
    public function updateTiket(Request $request, $id)
    {
        $tiket = InputTiket::with('stopclocks')->findOrFail($id);

        $data = $request->validate([
            'lokasi_id' => 'required|exists:lokasis,id',
            'open_tiket' => 'required|date',
            'link_up' => 'nullable|date',
            'link_upGSM' => 'nullable|date',
            'penyebab' => 'nullable|string',
            'action' => 'nullable|string',
            'status_koneksi' => 'nullable|string',
            'jenis_gangguan' => 'nullable|string',
            'status_tiket' => 'nullable|string',
            'action_images.*' => 'image|max:2048',
        ]);

        $existingImages = json_decode($tiket->action_images ?? '[]', true);

        if ($request->filled('deleted_images')) {
            $deleted = explode(',', $request->input('deleted_images'));
            foreach ($deleted as $img) {
                if (Storage::disk('public')->exists($img)) {
                    Storage::disk('public')->delete($img);
                }
                $existingImages = array_filter($existingImages, fn($e) => $e !== $img);
            }
            $existingImages = array_values($existingImages);
        }

        foreach ($request->file('action_images', []) as $file) {
            if (count($existingImages) >= 15) break;
            $existingImages[] = $file->store('action_images', 'public');
        }

        $tiket->stopclocks()->delete();
        $totalStopclock = $this->storeStopclocks($request, $tiket->id);

        $statusTiket = $request->filled('link_up') ? 'Selesai' : $request->input('status_tiket', $tiket->status_tiket);

        $updateData = $data;
        $updateData['status_tiket'] = $statusTiket;
        $updateData['action_images'] = json_encode($existingImages);
        $updateData['stopclock'] = Stopclock::where('input_tiket_id', $tiket->id)->count() . 'x stopclock';

        $tiket->update($updateData);

        // Panggil updateDurasi setelah update tiket untuk memastikan data terbaru digunakan
        $this->updateDurasi($tiket, $totalStopclock);

        $this->createTiketLog($tiket->id, $statusTiket, Auth::user()->email);

        return $tiket;
    }

    /**
     * Mengambil data untuk halaman edit.
     *
     * @param int $id
     * @return array
     */
    public function getEditData($id)
    {
        $tiket = InputTiket::with('stopclocks')->findOrFail($id);

        $tiket->formatted_open = $tiket->open_tiket ? Carbon::parse($tiket->open_tiket)->format('Y-m-d\TH:i') : '';
        $tiket->formatted_link_up = $tiket->link_up ? Carbon::parse($tiket->link_up)->format('Y-m-d\TH:i') : '';
        $tiket->formatted_link_upGSM = $tiket->link_upGSM ? Carbon::parse($tiket->link_upGSM)->format('Y-m-d\TH:i') : '';
        
        $tiket->durasi_display = $tiket->durasi ?? '';
        $tiket->durasi_gsm_display = $tiket->durasi_GSM ?? ''; // Tampilkan durasi_GSM

        return [
            'tiket' => $tiket,
            'lokasis' => Lokasi::all(),
        ];
    }

    /**
     * Menghapus tiket.
     *
     * @param int $id
     * @return void
     */
    public function deleteTiket($id)
    {
        $tiket = InputTiket::findOrFail($id);

        foreach (json_decode($tiket->action_images ?? '[]') as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $tiket->delete();
    }

    /**
     * Menyimpan data stopclock.
     *
     * @param Request $request
     * @param int $tiketId
     * @return int
     */
    public function storeStopclocks(Request $request, $tiketId)
    {
        $startClocks = $request->input('stopclocks.start_clock', []);
        $stopClocks = $request->input('stopclocks.stop_clock', []);
        $alasans = $request->input('stopclocks.alasan', []);

        $loopCount = min(count($startClocks), count($stopClocks), count($alasans));
        $data = [];
        $totalStopclock = 0;

        for ($i = 0; $i < $loopCount; $i++) {
            $start = $startClocks[$i];
            $stop = $stopClocks[$i];
            $alasan = $alasans[$i];

            if (!$start || !$stop || !$alasan) continue;

            try {
                $startTime = Carbon::parse($start);
                $stopTime = Carbon::parse($stop);
            } catch (\Exception $e) {
                throw ValidationException::withMessages([
                    "stopclocks.start_clock.$i" => "Format waktu tidak valid pada Stopclock ke-" . ($i + 1),
                ]);
            }

            if ($stopTime->lte($startTime)) {
                throw ValidationException::withMessages([
                    "stopclocks.stop_clock.$i" => "Stop Clock ke-" . ($i + 1) . " harus lebih besar dari Start Clock.",
                ]);
            }

            $durasiMenit = $startTime->diffInMinutes($stopTime);
            $totalStopclock += $durasiMenit;

            $data[] = [
                'input_tiket_id' => $tiketId,
                'start_clock' => $startTime,
                'stop_clock' => $stopTime,
                'alasan' => $alasan,
                'durasi' => $durasiMenit,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($data)) {
            Stopclock::insert($data);
        }

        return $totalStopclock;
    }

    /**
     * Mengupdate durasi FO dan GSM secara terpisah.
     *
     * @param InputTiket $tiket
     * @param int $totalStopclock
     * @return void
     */
    public function updateDurasi($tiket, $totalStopclock)
    {
        $updateData = [];

        // Logika untuk durasi FO (kolom 'durasi')
        if ($tiket->link_up) {
            $start = Carbon::parse($tiket->open_tiket);
            $end = Carbon::parse($tiket->link_up);
            $totalMinutes = $start->diffInMinutes($end);
            $finalMinutes = max($totalMinutes - $totalStopclock, 0);
            $updateData['durasi'] = $this->formatMinutesToDuration($finalMinutes);
        } else {
            $updateData['durasi'] = null;
        }

        // Logika untuk durasi GSM (kolom 'durasi_GSM')
        if ($tiket->link_upGSM) {
            $start = Carbon::parse($tiket->open_tiket);
            $end = Carbon::parse($tiket->link_upGSM);
            $totalMinutes = $start->diffInMinutes($end);
            $finalMinutes = max($totalMinutes - $totalStopclock, 0);
            $updateData['durasi_GSM'] = $this->formatMinutesToDuration($finalMinutes);
        } else {
            $updateData['durasi_GSM'] = null;
        }

        // Update tiket dengan data durasi yang sudah dihitung
        $tiket->update($updateData);
    }

    /**
     * Mengubah total menit menjadi string durasi "hari jam menit".
     *
     * @param int $totalMinutes
     * @return string
     */
    protected function formatMinutesToDuration($totalMinutes)
    {
        if ($totalMinutes < 0) {
            return "0 menit";
        }
        
        $hari = floor($totalMinutes / 1440);
        $sisaMenitSetelahHari = $totalMinutes % 1440;
        $jam = floor($sisaMenitSetelahHari / 60);
        $menit = $sisaMenitSetelahHari % 60;
    
        $durasiString = '';
        if ($hari > 0) {
            $durasiString .= "$hari hari ";
        }
        if ($jam > 0) {
            $durasiString .= "$jam jam ";
        }
        if ($menit > 0 || ($hari == 0 && $jam == 0)) {
            $durasiString .= "$menit menit";
        }
    
        return trim($durasiString);
    }

    /**
     * Membuat log tiket.
     *
     * @param int $tiketId
     * @param string $status
     * @param string $email
     * @return void
     */
    public function createTiketLog($tiketId, $status, $email)
    {
        TiketLog::create([
            'input_tiket_id' => $tiketId,
            'status' => $status ?? 'Update Tiket',
            'keterangan' => 'Tiket diperbarui oleh ' . $email,
            'waktu_log' => now(),
        ]);
    }
}
