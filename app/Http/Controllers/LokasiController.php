<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LokasiService;

class LokasiController extends Controller
{
    protected $lokasiService;

    public function __construct(LokasiService $lokasiService)
    {
        $this->lokasiService = $lokasiService;
    }

    public function index()
    {
        $lokasis = $this->lokasiService->getAll();
        return view('lokasi.index', compact('lokasis'));
    }

    public function create()
    {
        $lastNumber = $this->lokasiService->getLastNumber();
        $kodeNo = $lastNumber ? ((int)$lastNumber) + 1 : 1;
        return view('lokasi.create', compact('kodeNo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no' => 'required|unique:lokasis,no',
            'lokasi' => 'required',
            'sid' => 'required|unique:lokasis,sid',
            'product' => 'nullable|string',
            'bandwith' => 'nullable|string',
            'kategori_layanan' => 'nullable|string',
            'standard_availability' => 'nullable|numeric',
            'realisasi_availability' => 'nullable|numeric',
        ]);

        $this->lokasiService->store($request->all());

        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $lokasi = $this->lokasiService->getById($id);
        return view('lokasi.edit', compact('lokasi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lokasi' => 'required',
            'sid' => 'required|unique:lokasis,sid,' . $id,
            'product' => 'nullable|string',
            'bandwith' => 'nullable|string',
            'kategori_layanan' => 'nullable|string',
            'standard_availability' => 'nullable|numeric',
            'realisasi_availability' => 'nullable|numeric',
        ]);

        $this->lokasiService->update($id, $request->all());

        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->lokasiService->delete($id);
        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil dihapus.');
    }
}
