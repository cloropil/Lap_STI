<?php

namespace App\Services;

use App\Models\InputTiket;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    public function getGrafikHarian(Carbon $startDate, Carbon $endDate)
    {
        $range = collect();
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $range->push($date->format('Y-m-d'));
        }

        return $range->map(function ($date) {
            return [
                'tanggal' => $date,
                'jumlah' => InputTiket::whereDate('open_tiket', $date)->count(),
                'selesai' => InputTiket::whereDate('open_tiket', $date)
                    ->where('status_tiket', 'Selesai')->count(),
                'proses' => InputTiket::whereDate('open_tiket', $date)
                    ->where('status_tiket', 'Proses')->count(),
            ];
        });
    }

    public function getStatistikTiket()
    {
        return [
            'totalTiket' => InputTiket::count(),
            'tiketHariIni' => InputTiket::whereDate('open_tiket', Carbon::today())->count(),
            'tiketProses' => InputTiket::where('status_tiket', 'Proses')->count(),
        ];
    }

    public function getJenisGangguan()
    {
        return InputTiket::select('jenis_gangguan', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_gangguan')
            ->orderByDesc('total')
            ->get();
    }

    public function getTiketBulanan()
    {
        return InputTiket::select(
                DB::raw("DATE_FORMAT(open_tiket, '%M') AS bulan"),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('open_tiket', now()->year)
            ->groupBy(DB::raw("DATE_FORMAT(open_tiket, '%M')"))
            ->orderBy(DB::raw("MIN(open_tiket)"))
            ->get();
    }

    public function getLastUpdated()
    {
        return InputTiket::latest('updated_at')->value('updated_at');
    }
}
