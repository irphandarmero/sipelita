<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\MasterLaporanController;
use App\Http\Controllers\UserController;
use App\Models\DetailLaporan;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// GUEST ROUTES
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// AUTHENTICATED ROUTES
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard Utama
    Route::get('/dashboard', function () {
        $totalLaporan  = DetailLaporan::count();
        $diajukanCount = DetailLaporan::where('status_laporan', 'Diajukan')->count();
        $diprosesCount = DetailLaporan::where('status_laporan', 'Diproses')->count();
        $selesaiCount  = DetailLaporan::where('status_laporan', 'Selesai')->count();

        $laporanTerbaru = DetailLaporan::with(['user', 'unitAsal', 'unitTujuan', 'masterLaporan'])
                            ->latest()
                            ->take(5)
                            ->get();

        return view('dashboard', compact(
            'totalLaporan',
            'diajukanCount',
            'diprosesCount',
            'selesaiCount',
            'laporanTerbaru'
        ));
    })->name('dashboard');

    // Transaksi Pelaporan (SI-PELITA)
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/create', [LaporanController::class, 'create'])->name('create');
        Route::post('/', [LaporanController::class, 'store'])->name('store');
        Route::get('/{id}', [LaporanController::class, 'show'])->name('show');
        Route::patch('/{id}/status', [LaporanController::class, 'updateStatus'])->name('updateStatus');
    });

    // Master Data Admin (CRUD Modul)
    Route::prefix('master')->name('master.')->group(function () {
        // Master Unit Kerja
        Route::resource('unit-kerja', UnitKerjaController::class)->except(['create', 'show', 'edit']);

        // Master Laporan Kategori
        Route::resource('master-laporan', MasterLaporanController::class)->except(['create', 'show', 'edit']);

        // Manajemen User
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
    });

});
