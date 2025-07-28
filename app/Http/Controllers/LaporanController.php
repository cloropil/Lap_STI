<?php

namespace App\Http\Controllers;

use App\Services\LaporanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    protected $laporanService;

    public function __construct(LaporanService $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    public function harian(Request $request)
    {
        $tanggal = $this->laporanService->getTanggal($request);

        $tikets = $this->laporanService->getTiketHarian($tanggal);
        $kegiatanHarian = $this->laporanService->getKegiatanHarian($tanggal);

        return view('laporan.harian', compact('tikets', 'tanggal', 'kegiatanHarian'));
    }

    public function cetak(Request $request)
    {
        $tanggal = $this->laporanService->getTanggal($request);

        $tikets = $this->laporanService->getTiketHarianLengkap($tanggal);
        $kegiatanHarian = $this->laporanService->getKegiatanHarian($tanggal);

        $pdf = Pdf::loadView('laporan.template_pdf', compact('tikets', 'tanggal', 'kegiatanHarian'))
                  ->setPaper('A4', 'portrait');

        return $pdf->download('laporan-harian-' . $tanggal . '.pdf');
    }
}
