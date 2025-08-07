<?php

namespace App\Services;

// Mengubah 'Inputtike' menjadi 'InputTiket'
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

        return $query->paginate(15);
    }

    public function getLaporanHarian(Request $request)
    {
        $tanggal = $request->input('tangram', now()->format('Y-m-d'));

        return [
            'tikets' => InputTiket::with('lokasi')->whereDate('open_tiket', $tanggal)->get(),
            'kegiatanHarian' => LaporanKegiatan::whereDate('tanggal', $tanggal)->get(),
            'tanggal' => $tanggal,
        ];
    }

    public function storeTiket(Request $request)
    {
        $data = $request->validate([
            'lokasi_id' => 'required|exists:lokasis,id',
            'no_tiket' => 'required|unique:input_tikets,no_tiket',
            'open_tiket' => 'required|date',
            'link_up' => 'nullable|date',
            'link_upGSM' => 'nullable|date', // Tambahkan validasi untuk link_upGSM
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

        // 💡 Logika baru: Tentukan status tiket berdasarkan link_up (FO)
        // Jika link_up (FO) diisi, status tiket menjadi 'Selesai'.
        // Jika tidak, status tiket akan mengikuti nilai dari form (default 'Proses' untuk staff).
        $statusTiket = $request->filled('link_up') ? 'Selesai' : $request->input('status_tiket', 'Proses');

        $tiket = InputTiket::create(array_merge($data, [
            'status_tiket' => $statusTiket, // Gunakan status tiket yang sudah dimodifikasi
            'action_images' => json_encode($imagePaths),
            'stopclock' => '0x stopclock',
        ]));

        $totalStopclock = $this->storeStopclocks($request, $tiket->id);
        
        // Panggil updateDurasi untuk menghitung atau mengosongkan durasi berdasarkan link_up (FO)
        $this->updateDurasi($tiket, $request->input('link_up'), $totalStopclock);
        
        $this->createTiketLog($tiket->id, $statusTiket, Auth::user()->email); // Gunakan status tiket yang sudah dimodifikasi

        return $tiket;
    }

    public function updateTiket(Request $request, $id)
    {
        $tiket = InputTiket::with('stopclocks')->findOrFail($id);

        $data = $request->validate([
            'lokasi_id' => 'required|exists:lokasis,id',
            'open_tiket' => 'required|date',
            'link_up' => 'nullable|date',
            'link_upGSM' => 'nullable|date', // Tambahkan validasi untuk link_upGSM
            'penyebab' => 'nullable|string', // Mengubah 'required' menjadi 'nullable' agar sesuai form
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

        // 💡 Logika baru: Tentukan status tiket berdasarkan link_up (FO)
        // Jika link_up (FO) diisi, status tiket menjadi 'Selesai'.
        // Jika tidak, status tiket akan mengikuti nilai dari form atau nilai yang ada di database.
        $statusTiket = $request->filled('link_up') ? 'Selesai' : $request->input('status_tiket', $tiket->status_tiket);
        
        $updateData = $data;
        $updateData['status_tiket'] = $statusTiket; // Gunakan status tiket yang sudah dimodifikasi
        $updateData['action_images'] = json_encode($existingImages);
        $updateData['stopclock'] = Stopclock::where('input_tiket_id', $tiket->id)->count() . 'x stopclock';

        $tiket->update($updateData);
        
        // Panggil updateDurasi setelah update tiket untuk memastikan data terbaru digunakan
        $this->updateDurasi($tiket, $request->input('link_up'), $totalStopclock);

        $this->createTiketLog($tiket->id, $statusTiket, Auth::user()->email); // Gunakan status tiket yang sudah dimodifikasi

        return $tiket;
    }

    public function getEditData($id)
    {
        $tiket = InputTiket::with('stopclocks')->findOrFail($id);

        $tiket->formatted_open = $tiket->open_tiket ? Carbon::parse($tiket->open_tiket)->format('Y-m-d\TH:i') : '';
        $tiket->formatted_link_up = $tiket->link_up ? Carbon::parse($tiket->link_up)->format('Y-m-d\TH:i') : '';
        $tiket->formatted_link_upGSM = $tiket->link_upGSM ? Carbon::parse($tiket->link_upGSM)->format('Y-m-d\TH:i') : ''; // Tambahkan field untuk link_upGSM
        
        $tiket->durasi_display = $tiket->durasi ?? ''; 

        return [
            'tiket' => $tiket,
            'lokasis' => Lokasi::all(),
        ];
    }

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

    public function updateDurasi($tiket, $linkUp, $totalStopclock)
    {
        // Kondisi utama: Jika linkUp (FO) kosong, set durasi menjadi NULL di database
        if (empty($linkUp)) {
            $tiket->update(['durasi' => null]);
            return; // Hentikan eksekusi fungsi
        }

        // Lanjutkan perhitungan durasi hanya jika linkUp tidak kosong
        $start = $tiket->open_tiket instanceof Carbon ? $tiket->open_tiket : Carbon::parse($tiket->open_tiket);
        $end = $linkUp instanceof Carbon ? $linkUp : Carbon::parse($linkUp);

        $totalMinutes = $start->diffInMinutes($end);
        $finalMinutes = max($totalMinutes - $totalStopclock, 0);

        // Perhitungan hari, jam, menit yang lebih robust
        $hari = floor($finalMinutes / 1440); // 1440 menit dalam sehari
        $sisaMenitSetelahHari = $finalMinutes % 1440;
        $jam = floor($sisaMenitSetelahHari / 60);
        $menit = $sisaMenitSetelahHari % 60;

        $durasiString = '';
        if ($hari > 0) {
            $durasiString .= "$hari hari ";
        }
        if ($jam > 0) {
            $durasiString .= "$jam jam ";
        }
        // Tampilkan menit jika ada, atau jika total durasi 0 (untuk "0 menit")
        if ($menit > 0 || ($hari == 0 && $jam == 0)) {
            $durasiString .= "$menit menit";
        }

        $tiket->update(['durasi' => trim($durasiString)]);
    }

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