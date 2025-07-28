<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        $endDate = $request->input('tanggal') 
            ? Carbon::parse($request->input('tanggal')) 
            : now();
        $startDate = $endDate->copy()->subDays(6);

        $grafikData = $this->dashboardService->getGrafikHarian($startDate, $endDate);
        $statistik = $this->dashboardService->getStatistikTiket();
        $gangguanData = $this->dashboardService->getJenisGangguan();
        $monthlyData = $this->dashboardService->getTiketBulanan();
        $lastUpdated = $this->dashboardService->getLastUpdated();

        $selectedDate = $endDate->format('Y-m-d');

        return view('dashboard', [
            'grafikData' => $grafikData,
            'selectedDate' => $selectedDate,
            'totalTiket' => $statistik['totalTiket'],
            'tiketHariIni' => $statistik['tiketHariIni'],
            'tiketProses' => $statistik['tiketProses'],
            'gangguanData' => $gangguanData,
            'monthlyData' => $monthlyData,
            'lastUpdated' => $lastUpdated,
        ]);
    }
}