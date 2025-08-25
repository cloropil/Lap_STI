<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\InputTiketController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AkunPenggunaController;
use App\Http\Controllers\LaporanKegiatanController;
use Illuminate\Support\Facades\Auth;
use App\Models\AkunPengguna;
use App\Models\Lokasi;

// === ROUTE LOGIN & LOGOUT ===
// Harus di luar middleware 'auth' supaya bisa diakses tanpa login
Route::get('/login', [AkunPenggunaController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AkunPenggunaController::class, 'login'])->name('login.post');
Route::post('/logout', [AkunPenggunaController::class, 'logout'])->name('logout')->middleware('auth');

// Redirect "/" ke dashboard
Route::get('/', fn () => redirect()->route('dashboard'));

// === ROUTE YANG PERLU LOGIN (middleware auth) ===
Route::middleware(['auth'])->group(function () {

Route::get('/dashboard/laporan-kegiatan/partial', [LaporanKegiatanController::class, 'partial'])
        ->name('dashboard.laporan-kegiatan.partial');

    // === DASHBOARD ===
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // === LAPORAN KEGIATAN (Resource) ===
    Route::resource('kegiatan', LaporanKegiatanController::class)->names('kegiatan');

    // === LOKASI ===
    Route::prefix('lokasi')->name('lokasi.')->group(function () {
        Route::get('/', [LokasiController::class, 'index'])->name('index');
        Route::get('/create', [LokasiController::class, 'create'])->name('create');
        Route::post('/store', [LokasiController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [LokasiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LokasiController::class, 'update'])->name('update');
        Route::delete('/{id}', [LokasiController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/info', [InputTiketController::class, 'info'])->name('info');

        // Route SID (hanya SID)
        Route::get('/{id}/sid', function ($id) {
            $lokasi = Lokasi::find($id);
            if ($lokasi && isset($lokasi->sid)) {
                return response()->json(['sid' => $lokasi->sid]);
            }
            return response()->json(['sid' => null], 404);
        });
    });

    // === INPUT TIKET ===
    Route::prefix('inputtiket')->name('inputtiket.')->group(function () {
        // Rute statis harus diletakkan di atas rute dinamis
        Route::get('/export', [InputTiketController::class, 'export'])->name('export');
        
        // Rute lain yang sudah ada
        Route::get('/', [InputTiketController::class, 'index'])->name('index');
        Route::get('/create', [InputTiketController::class, 'create'])->name('create');
        Route::post('/store', [InputTiketController::class, 'store'])->name('store');
        
        // Rute dinamis diletakkan di bagian bawah
        Route::get('/{id}', [InputTiketController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [InputTiketController::class, 'edit'])->name('edit');
        Route::put('/{id}', [InputTiketController::class, 'update'])->name('update');
        Route::delete('/{id}', [InputTiketController::class, 'destroy'])->name('destroy');
    });


    // === STOPCLOCK ===
    Route::prefix('stopclock')->name('stopclock.')->group(function () {
        Route::post('/{tiket_id}/store', [InputTiketController::class, 'store'])->name('store');
        Route::delete('/{id}', [InputTiketController::class, 'destroy'])->name('destroy');
    });

    // === LOG TIKET ===
    Route::prefix('tiketlog')->name('tiketlog.')->group(function () {
        Route::get('/{tiket_id}', [InputTiketController::class, 'index'])->name('index');
        Route::post('/{tiket_id}/store', [InputTiketController::class, 'store'])->name('store');
    });

    // === LAPORAN HARIAN ===
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/harian', [LaporanController::class, 'harian'])->name('harian');
        Route::get('/harian/cetak', [LaporanController::class, 'cetak'])->name('cetak'); // PDF langsung diunduh
    });

    // === LAPORAN KEGIATAN ===
    Route::prefix('laporankegiatan')->name('laporankegiatan.')->group(function () {
        Route::get('/', [LaporanKegiatanController::class, 'index'])->name('index');
        Route::get('/create', [LaporanKegiatanController::class, 'create'])->name('create');
        Route::post('/', [LaporanKegiatanController::class, 'store'])->name('store');
        Route::get('/{id}', [LaporanKegiatanController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [LaporanKegiatanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LaporanKegiatanController::class, 'update'])->name('update');
        Route::delete('/{id}', [LaporanKegiatanController::class, 'destroy'])->name('destroy');
    });    
       // === AKUN PENGGUNA ===
    Route::resource('akun-pengguna', AkunPenggunaController::class)->except(['show'])->names('akun-pengguna');
    Route::get('/akun-pengguna/settings', [AkunPenggunaController::class, 'settings'])->name('akun-pengguna.settings');
    Route::put('/akun-pengguna/settings', [AkunPenggunaController::class, 'updateSettings'])->name('akun-pengguna.settings.update');
});