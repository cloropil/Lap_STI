<?php

namespace App\Http\Controllers;

use App\Models\InputTiket;
use App\Models\Lokasi;
use App\Services\InputTiketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InputTiketController extends Controller
{
    protected $service;

    public function __construct(InputTiketService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $tikets = $this->service->getFilteredTikets($request);
        return view('inputtiket.index', compact('tikets'));
    }

    public function laporanHarian(Request $request)
    {
        $data = $this->service->getLaporanHarian($request);
        return view('laporan.harian', $data);
    }

    public function create()
    {
        $lokasis = Lokasi::all();
        return view('inputtiket.create', compact('lokasis'));
    }

    public function store(Request $request)
    {
        $this->service->storeTiket($request);
        return redirect()->route('inputtiket.index')->with('success', 'Tiket berhasil disimpan.');
    }

    public function edit($id)
    {
        $data = $this->service->getEditData($id);
        return view('inputtiket.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->service->updateTiket($request, $id);
        return redirect()->route('inputtiket.index')->with('success', 'Tiket berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->service->deleteTiket($id);
        return redirect()->route('inputtiket.index')->with('success', 'Tiket berhasil dihapus.');
    }

    public function show($id)
    {
        $tiket = InputTiket::with(['lokasi', 'stopclocks'])->findOrFail($id);
        return view('inputtiket.show', compact('tiket'));
    }

}
