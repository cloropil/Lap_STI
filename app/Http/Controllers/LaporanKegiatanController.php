<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanKegiatan;
use App\Services\LaporanKegiatanService;

class LaporanKegiatanController extends Controller
{
    protected $service;

    public function __construct(LaporanKegiatanService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $selectedDate = $request->input('tanggal');

        $query = LaporanKegiatan::query();

        if ($selectedDate) {
            $query->whereDate('tanggal', $selectedDate);
        }

        $laporans = $query->latest()->paginate(5);

        return view('kegiatan.index', compact('laporans', 'selectedDate'));
    }

public function ajax(Request $request)
{
    $tanggal = $request->input('tanggal', now()->toDateString());

    $query = LaporanKegiatan::query();

    if ($tanggal) {
        $query->whereDate('tanggal', $tanggal);
    }

    $laporans = $query->latest()->paginate(5);

    return view('dashboard.partials.laporan_kegiatan', [
        'laporans' => $laporans,
        'selectedDate' => $tanggal
    ]);
}

public function partial(Request $request)
{
    $query = LaporanKegiatan::query();

    // Filter berdasarkan tanggal
    if ($request->filled('tanggal')) {
        $query->whereDate('tanggal', $request->tanggal);
    }

    // Filter berdasarkan keyword pada judul atau kegiatan
    if ($request->filled('keyword')) {
        $keyword = $request->keyword;
        $query->where(function ($q) use ($keyword) {
            $q->where('judul', 'like', "%{$keyword}%")
              ->orWhere('kegiatan', 'like', "%{$keyword}%");
        });
    }

    // Ambil hasil dengan pagination dan tetap mempertahankan query string
    $laporans = $query->latest()->paginate(5)->withQueryString();

    // Kirim data ke blade
    $selectedDate = $request->tanggal;
    $keyword = $request->keyword;

    return view('dashboard.partials.laporan_kegiatan', compact('laporans', 'selectedDate', 'keyword'));
}


    public function create()
    {
        return view('kegiatan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kegiatan' => 'required|string',
            'judul' => 'nullable|string',
            'apa' => 'nullable|string',
            'siapa' => 'nullable|string',
            'kapan' => 'nullable|string',
            'dimana' => 'nullable|string',
            'mengapa' => 'nullable|string',
            'bagaimana' => 'nullable|string',
        ]);

        $this->service->store($request);

        return redirect()->route('kegiatan.index')->with('success', 'Laporan kegiatan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $laporan = LaporanKegiatan::findOrFail($id);
        return view('kegiatan.show', compact('laporan'));
    }

    public function edit($id)
    {
        $laporan = LaporanKegiatan::findOrFail($id);
        return view('kegiatan.edit', compact('laporan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kegiatan' => 'required|string',
            'judul' => 'nullable|string',
            'apa' => 'nullable|string',
            'siapa' => 'nullable|string',
            'kapan' => 'nullable|string',
            'dimana' => 'nullable|string',
            'mengapa' => 'nullable|string',
            'bagaimana' => 'nullable|string',
        ]);

        $laporan = LaporanKegiatan::findOrFail($id);
        $this->service->update($request, $laporan);

        return redirect()->route('kegiatan.index')->with('success', 'Laporan kegiatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $laporan = LaporanKegiatan::findOrFail($id);
        $this->service->delete($laporan);

        return redirect()->route('kegiatan.index')->with('success', 'Laporan kegiatan berhasil dihapus.');
    }
}
