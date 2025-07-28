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
        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));

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

        $tiket = InputTiket::create(array_merge($data, [
            'action_images' => json_encode($imagePaths),
            'stopclock' => '0x stopclock',
            'durasi' => 'Belum dihitung',
        ]));

        $totalStopclock = $this->storeStopclocks($request, $tiket->id);
        $this->updateDurasi($tiket, $request->input('link_up', $tiket->link_up), $totalStopclock);
        $this->createTiketLog($tiket->id, $request->input('status_tiket'), Auth::user()->email);

        return $tiket;
    }

    public function updateTiket(Request $request, $id)
    {
        $tiket = InputTiket::with('stopclocks')->findOrFail($id);

        $data = $request->validate([
            'lokasi_id' => 'required|exists:lokasis,id',
            'open_tiket' => 'required|date',
            'link_up' => 'nullable|date',
            'penyebab' => 'required|string',
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
        $this->updateDurasi($tiket, $request->input('link_up', $tiket->link_up), $totalStopclock);

        $updateData = $data;
        $updateData['action_images'] = json_encode($existingImages);
        $updateData['stopclock'] = Stopclock::where('input_tiket_id', $tiket->id)->count() . 'x stopclock';

        $tiket->update($updateData);

        $this->createTiketLog($tiket->id, $request->input('status_tiket'), Auth::user()->email);

        return $tiket;
    }

    public function getEditData($id)
    {
        $tiket = InputTiket::with('stopclocks')->findOrFail($id);

        $tiket->formatted_open = $tiket->open_tiket ? Carbon::parse($tiket->open_tiket)->format('Y-m-d\TH:i') : '';
        $tiket->formatted_link_up = $tiket->link_up ? Carbon::parse($tiket->link_up)->format('Y-m-d\TH:i') : '';
        $tiket->durasi_display = $tiket->durasi ?? 'Belum dihitung';

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
        $start = $tiket->open_tiket instanceof Carbon ? $tiket->open_tiket : Carbon::parse($tiket->open_tiket);
        $end = $linkUp
            ? ($linkUp instanceof Carbon ? $linkUp : Carbon::parse($linkUp))
            : now();

        $totalMinutes = $start->diffInMinutes($end);
        $finalMinutes = max($totalMinutes - $totalStopclock, 0);
        $jam = floor($finalMinutes / 60);
        $menit = $finalMinutes % 60;

        $durasi = ($jam > 0 ? "$jam Jam " : '') . "$menit Menit";
        $tiket->update(['durasi' => $durasi]);
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
