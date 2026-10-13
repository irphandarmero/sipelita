<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\DisposisiLaporanController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\MasterLaporanController;
use App\Http\Controllers\UserController;
use App\Models\DetailLaporan;

/*
|--------------------------------------------------------------------------
| Web Routes - SI-PELITA RS
|--------------------------------------------------------------------------
*/

// Redirect halaman utama '/' ke login atau dashboard
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// --------------------------------------------------------------------------
// 1. GUEST ROUTES (Hanya bisa diakses jika belum login)
// --------------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// --------------------------------------------------------------------------
// 2. AUTHENTICATED ROUTES (Wajib Login)
// --------------------------------------------------------------------------
Route::middleware('auth')->group(function () {

    // Logout & Ubah Password
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/password/change', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/password/change', [AuthController::class, 'updatePassword'])->name('password.update');

    // Dashboard Utama
    Route::get('/dashboard', function () {
        $laporanQuery = DetailLaporan::query();
        if (!in_array(Auth::user()->role, ['admin', 'direksi'])) {
            $unitId = Auth::user()->unit_kerja_id;
            $laporanQuery->where(function ($query) use ($unitId) {
                $query->where('user_id', Auth::id())
                    ->orWhere('unit_asal_id', $unitId)
                    ->orWhere('unit_tujuan_id', $unitId)
                    ->orWhereHas('disposisiLaporan', function ($disposisiQuery) use ($unitId) {
                        $disposisiQuery->where('unit_pengirim_id', $unitId)
                            ->orWhere('unit_penerima_id', $unitId);
                    });
            });
        }

        $totalLaporan  = (clone $laporanQuery)->count();
        $diajukanCount = (clone $laporanQuery)->where('status_laporan', 'Diajukan')->count();
        $diprosesCount = (clone $laporanQuery)->where('status_laporan', 'Diproses')->count();
        $selesaiCount  = (clone $laporanQuery)->where('status_laporan', 'Selesai')->count();

        $laporanTerbaru = (clone $laporanQuery)->with(['user', 'unitAsal', 'unitTujuan', 'masterLaporan'])
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

    // ----------------------------------------------------------------------
    // Transaksi Pelaporan (SI-PELITA)
    // ----------------------------------------------------------------------
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');             // Daftar Laporan
        Route::get('/create', [LaporanController::class, 'create'])->name('create');     // Form Tambah
        Route::post('/', [LaporanController::class, 'store'])->name('store');            // Simpan Laporan
        Route::get('/{id}', [LaporanController::class, 'show'])->name('show');          // Detail & Audit Trail
        Route::patch('/{id}/status', [LaporanController::class, 'updateStatus'])->name('updateStatus'); // Update Status
    });
    // ----------------------------------------------------------------------
    // Disposisi Laporan Lintas-Unit
    // ----------------------------------------------------------------------
    Route::prefix('disposisi')->name('disposisi.')->group(function () {
        Route::post('/{laporanId}', [DisposisiLaporanController::class, 'store'])->name('store');            // Kirim Disposisi Baru
        Route::patch('/{id}/status', [DisposisiLaporanController::class, 'updateStatus'])->name('updateStatus'); // Update Status Disposisi
        Route::delete('/{id}', [DisposisiLaporanController::class, 'destroy'])->name('destroy');             // Batal/Hapus Disposisi
    });

    // 
    // ----------------------------------------------------------------------
    // Master Data (Khusus Admin)
    // ----------------------------------------------------------------------
    Route::prefix('master')->name('master.')->middleware('admin')->group(function () {
        Route::resource('unit-kerja', UnitKerjaController::class)->except(['create', 'show', 'edit']);
        Route::resource('master-laporan', MasterLaporanController::class)->except(['create', 'show', 'edit']);
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
    });

});
