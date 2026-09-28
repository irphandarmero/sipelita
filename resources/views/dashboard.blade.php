@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
<!-- Welcome Banner -->
<div class="card border-0 bg-primary text-white shadow-sm mb-4" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bi bi-speedometer2 me-2"></i>Selamat Datang di SI-PELITA, {{ Auth::user()->name ?? 'Pengguna' }}!
                </h4>
                <p class="mb-0 text-white-50 small">
                    Sistem Pelaporan & Informasi Terpadu Antar-Unit — 
                    <strong>{{ Auth::user()->unitKerja->nama_unit ?? 'Unit Kerja' }}</strong>
                </p>
            </div>
            <div>
                <a href="{{ route('laporan.create') }}" class="btn btn-light text-primary fw-bold shadow-sm px-3">
                    <i class="bi bi-plus-lg me-1"></i> Buat Laporan Baru
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 1. STATISTIC CARDS (RINGKASAN LAPORAN)     -->
<!-- ========================================== -->
<div class="row g-3 mb-4">
    <!-- Card Total Laporan -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase fs-7 fw-bold text-muted mb-1">Total Laporan</div>
                        <h3 class="fw-bold text-dark mb-0">{{ $totalLaporan ?? 0 }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-file-earmark-text fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Laporan Diajukan (Baru) -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase fs-7 fw-bold text-muted mb-1">Diajukan (Menunggu)</div>
                        <h3 class="fw-bold text-warning mb-0">{{ $diajukanCount ?? 0 }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Laporan Diproses -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase fs-7 fw-bold text-muted mb-1">Sedang Diproses</div>
                        <h3 class="fw-bold text-info mb-0">{{ $diprosesCount ?? 0 }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-gear-wide-connected fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Laporan Selesai -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase fs-7 fw-bold text-muted mb-1">Selesai Ditangani</div>
                        <h3 class="fw-bold text-success mb-0">{{ $selesaiCount ?? 0 }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. RECENT REPORTS & QUICK NAVIGATION       -->
<!-- ========================================== -->
<div class="row g-4">
    <!-- Tabel 5 Laporan Terbaru -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-clock-history text-primary me-2"></i>Laporan Terbaru Masuk
                </h6>
                <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-outline-primary fw-semibold">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Kode TRX</th>
                                <th>Judul Laporan</th>
                                <th>Unit Asal ➔ Tujuan</th>
                                <th>Prioritas</th>
                                <th class="pe-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($laporanTerbaru ?? [] as $laporan)
                                <tr>
                                    <td class="ps-3 fw-bold text-primary">
                                        <a href="{{ route('laporan.show', $laporan->id) }}" class="text-decoration-none">
                                            {{ $laporan->kode_transaksi }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $laporan->judul_laporan }}</div>
                                        <small class="text-muted">{{ $laporan->masterLaporan->nama_laporan ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                            {{ $laporan->unitAsal->nama_unit ?? '-' }}
                                        </span>
                                        <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                            {{ $laporan->unitTujuan->nama_unit ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($laporan->prioritas == 'Darurat')
                                            <span class="badge bg-danger">Darurat</span>
                                        @elseif($laporan->prioritas == 'Tinggi')
                                            <span class="badge bg-warning text-dark">Tinggi</span>
                                        @elseif($laporan->prioritas == 'Sedang')
                                            <span class="badge bg-info text-dark">Sedang</span>
                                        @else
                                            <span class="badge bg-secondary">Rendah</span>
                                        @endif
                                    </td>
                                    <td class="pe-3">
                                        @if($laporan->status_laporan == 'Diajukan')
                                            <span class="badge bg-warning text-dark">Diajukan</span>
                                        @elseif($laporan->status_laporan == 'Diproses')
                                            <span class="badge bg-info text-dark">Diproses</span>
                                        @elseif($laporan->status_laporan == 'Selesai')
                                            <span class="badge bg-success">Selesai</span>
                                        @else
                                            <span class="badge bg-danger">Ditolak</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Belum ada aktivitas laporan terbaru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Widget: Akses Cepat & Panduan -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-lightning-charge text-warning me-2"></i>Pintasan Akses Cepat
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="d-grid gap-2">
                    <a href="{{ route('laporan.create') }}" class="btn btn-outline-primary text-start p-2">
                        <i class="bi bi-plus-circle-fill fs-5 me-2 align-middle text-primary"></i>
                        <span class="fw-semibold">Buat Aduan / Laporan Baru</span>
                    </a>
                    
                    @if(in_array(Auth::user()->role ?? '', ['admin', 'petugas', 'kepala_unit']))
                    <a href="{{ route('laporan.index', ['view_type' => 'incoming']) }}" class="btn btn-outline-warning text-dark text-start p-2">
                        <i class="bi bi-inbox-fill fs-5 me-2 align-middle text-warning"></i>
                        <span class="fw-semibold">Lihat Laporan Masuk Unit</span>
                    </a>
                    @endif

                    <a href="{{ route('laporan.index', ['jenis' => 'Rutin']) }}" class="btn btn-outline-info text-dark text-start p-2">
                        <i class="bi bi-bar-chart-line-fill fs-5 me-2 align-middle text-info"></i>
                        <span class="fw-semibold">Rekap Sensus & Laporan Rutin</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Card Alur Kerja SI-PELITA -->
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body p-3">
                <h6 class="fw-bold text-dark mb-2">
                    <i class="bi bi-info-circle text-info me-2"></i>Alur Singkat SI-PELITA
                </h6>
                <ol class="small text-muted ps-3 mb-0">
                    <li class="mb-1">Unit pelapor mengisi form aduan / laporan rutin.</li>
                    <li class="mb-1">Notifikasi dikirim ke Unit Penanggung Jawab (IT/IPSRS/dll).</li>
                    <li class="mb-1">Teknisi memproses & memperbarui status aduan.</li>
                    <li>Sistem mencatat audit trail otomatis hingga laporan selesai.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
