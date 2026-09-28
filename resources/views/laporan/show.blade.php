@extends('layouts.app')

@section('title', 'Detail Laporan #' . $laporan->kode_transaksi)

@section('content')
<div class="mb-4">
    <!-- Top Action Bar & Breadcrumb -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary btn-sm me-1" title="Kembali">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0 text-dark">
                    Laporan <span class="text-primary">#{{ $laporan->kode_transaksi }}</span>
                </h4>
            </div>
            <p class="text-muted mb-0 small">
                Dikirim pada {{ $laporan->tanggal_kejadian ? $laporan->tanggal_kejadian->format('d F Y, H:i') : '-' }} WIB
            </p>
        </div>

        <!-- Badges & Modal Trigger Button -->
        <div class="d-flex align-items-center gap-2">
            <!-- Priority Badge -->
            @if($laporan->prioritas == 'Darurat')
                <span class="badge bg-danger text-white fs-6 px-3 py-2"><i class="bi bi-exclamation-octagon-fill me-1"></i>Darurat 🔥</span>
            @elseif($laporan->prioritas == 'Tinggi')
                <span class="badge bg-warning text-dark fs-6 px-3 py-2">Prioritas Tinggi</span>
            @elseif($laporan->prioritas == 'Sedang')
                <span class="badge bg-info text-dark fs-6 px-3 py-2">Prioritas Sedang</span>
            @else
                <span class="badge bg-secondary fs-6 px-3 py-2">Prioritas Rendah</span>
            @endif

            <!-- Status Badge -->
            @if($laporan->status_laporan == 'Diajukan')
                <span class="badge bg-warning text-dark fs-6 px-3 py-2"><i class="bi bi-hourglass-split me-1"></i>Diajukan</span>
            @elseif($laporan->status_laporan == 'Diproses')
                <span class="badge bg-info text-dark fs-6 px-3 py-2"><i class="bi bi-gear-wide-connected me-1"></i>Diproses</span>
            @elseif($laporan->status_laporan == 'Selesai')
                <span class="badge bg-success fs-6 px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i>Selesai</span>
            @elseif($laporan->status_laporan == 'Ditolak')
                <span class="badge bg-danger fs-6 px-3 py-2"><i class="bi bi-x-circle-fill me-1"></i>Ditolak</span>
            @endif

            <!-- Update Status Modal Button -->
            @if(in_array(Auth::user()->role ?? '', ['admin', 'petugas', 'kepala_unit']))
                <button type="button" class="btn btn-primary fw-semibold shadow-sm px-3 ms-2" data-bs-toggle="modal" data-bs-target="#modalUpdateStatus">
                    <i class="bi bi-pencil-square me-1"></i> Tindak Lanjut
                </button>
            @endif
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- LEFT COLUMN: DETAIL CONTENT -->
    <div class="col-lg-7">
        <!-- 1. INFORMASI UTAMA & RINCIAN KEJADIAN -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 fs-7">
                        {{ $laporan->masterLaporan->jenis_laporan ?? 'Kategori' }}
                    </span>
                    <small class="text-muted">
                        Kategori: <strong>{{ $laporan->masterLaporan->nama_laporan ?? 'N/A' }}</strong>
                    </small>
                </div>
                <h5 class="fw-bold text-dark mt-2 mb-1">{{ $laporan->judul_laporan }}</h5>
            </div>

            <div class="card-body p-4">
                <!-- Deskripsi Kejadian -->
                <div class="mb-4">
                    <label class="fw-bold text-secondary small text-uppercase mb-2">Deskripsi & Rincian Kejadian</label>
                    <div class="p-3 bg-light rounded text-dark lh-base border" style="white-space: pre-line;">
                        {{ $laporan->deskripsi_kejadian }}
                    </div>
                </div>

                <!-- Tindakan Awal -->
                @if($laporan->tindakan_awal)
                    <div class="mb-4">
                        <label class="fw-bold text-secondary small text-uppercase mb-2">Tindakan Awal Yang Dilakukan Pelapor</label>
                        <div class="p-3 bg-warning bg-opacity-10 text-dark rounded border border-warning border-opacity-25 lh-base">
                            <i class="bi bi-info-circle text-warning me-2"></i>{{ $laporan->tindakan_awal }}
                        </div>
                    </div>
                @endif

                <!-- Lampiran / Foto Proof -->
                @if($laporan->lampiran)
                    <div>
                        <label class="fw-bold text-secondary small text-uppercase mb-2">Lampiran Bukti / File</label>
                        <div class="p-3 border rounded bg-light d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-earmark-image fs-2 text-primary me-3"></i>
                                <div>
                                    <div class="fw-semibold text-dark mb-0">Berkas Lampiran Laporan</div>
                                    <small class="text-muted">{{ basename($laporan->lampiran) }}</small>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $laporan->lampiran) }}" target="_blank" class="btn btn-sm btn-outline-primary fw-semibold">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Buka Lampiran
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- 2. METADATA STAF & UNIT KERJA (Model Relations) -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="bi bi-people-fill text-primary me-2"></i>Informasi Aktor & Unit Kerja
                </h6>
                <div class="row g-3">
                    <!-- Unit Asal & Pelapor -->
                    <div class="col-md-6 border-end">
                        <small class="text-muted d-block uppercase fs-7">Unit Asal (Pengirim):</small>
                        <div class="fw-bold text-dark fs-6">{{ $laporan->unitAsal->nama_unit ?? 'N/A' }}</div>
                        <small class="text-muted d-block mt-2 fs-7">User Pelapor:</small>
                        <div class="fw-semibold text-primary">
                            <i class="bi bi-person-fill me-1"></i>{{ $laporan->user->name ?? 'Anonim' }}
                        </div>
                        <small class="text-muted">({{ $laporan->user->email ?? '-' }})</small>
                    </div>

                    <!-- Unit Tujuan & Petugas Penangan -->
                    <div class="col-md-6 ps-md-4">
                        <small class="text-muted d-block uppercase fs-7">Unit Tujuan (Penanggung Jawab):</small>
                        <div class="fw-bold text-dark fs-6">{{ $laporan->unitTujuan->nama_unit ?? 'N/A' }}</div>
                        <small class="text-muted d-block mt-2 fs-7">Petugas / Teknisi Penangan:</small>
                        <div class="fw-semibold text-success">
                            <i class="bi bi-person-badge me-1"></i>{{ $laporan->petugasPenangan->name ?? 'Belum Ditugaskan' }}
                        </div>
                        @if($laporan->tanggal_penyelesaian)
                            <small class="text-muted d-block mt-1">
                                Diselesaikan: {{ $laporan->tanggal_penyelesaian->format('d/m/Y H:i') }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: AUDIT TRAIL / TIMELINE PROGRESS -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h6 class="fw-bold text-dark mb-1">
                    <i class="bi bi-clock-history text-primary me-2"></i>Audit Trail / Timeline Progress
                </h6>
                <small class="text-muted">Rekam jejak kronologis perubahan status laporan dari awal hingga selesai.</small>
            </div>

            <div class="card-body p-4">
                <div class="position-relative ps-3 ms-2 border-start border-2 border-primary border-opacity-25" style="min-height: 250px;">
                    @forelse($laporan->riwayatLaporan as $riwayat)
                        <div class="mb-4 position-relative">
                            <!-- Bullet Indicator Icon -->
                            <div class="position-absolute top-0 start-0 translate-middle-x bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                                 style="width: 24px; height: 24px; font-size: 0.7rem; left: -1px;">
                                <i class="bi bi-check-lg"></i>
                            </div>

                            <!-- Timeline Item Content -->
                            <div class="ps-3">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge bg-dark bg-opacity-75 text-white fs-8">
                                        {{ $riwayat->status_baru }}
                                    </span>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        {{ $riwayat->created_at ? $riwayat->created_at->format('d/m/Y H:i') : '' }}
                                    </small>
                                </div>
                                <div class="fw-semibold text-dark fs-7">
                                    {{ $riwayat->user->name ?? 'Sistem' }}
                                    <small class="text-muted fw-normal">({{ $riwayat->user->unitKerja->nama_unit ?? '' }})</small>
                                </div>
                                <p class="text-secondary small mb-0 mt-1 bg-light p-2 rounded border">
                                    "{{ $riwayat->keterangan }}"
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-hourglass text-secondary opacity-50 fs-2 d-block mb-2"></i>
                            Belum ada catatan riwayat penanganan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL UPDATE STATUS & CATATAN PENANGANAAN -->
<!-- ========================================== -->
@if(in_array(Auth::user()->role ?? '', ['admin', 'petugas', 'kepala_unit']))
<div class="modal fade" id="modalUpdateStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('laporan.updateStatus', $laporan->id) }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="modal-header bg-primary text-white">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square me-2"></i>Tindak Lanjut Laporan #{{ $laporan->kode_transaksi }}
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="status_baru" class="form-label fw-semibold">Pilih Status Baru <span class="text-danger">*</span></label>
                        <select name="status_baru" id="status_baru" class="form-select" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="Diproses" {{ $laporan->status_laporan == 'Diproses' ? 'selected' : '' }}>Diproses (Sedang Ditangani)</option>
                            <option value="Selesai" {{ $laporan->status_laporan == 'Selesai' ? 'selected' : '' }}>Selesai (Tuntas Ditangani)</option>
                            <option value="Ditolak" {{ $laporan->status_laporan == 'Ditolak' ? 'selected' : '' }}>Ditolak (Tidak Valid/Bukan Wewenang)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label fw-semibold">Catatan / Keterangan Tindakan <span class="text-danger">*</span></label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="form-control" 
                                  placeholder="Tuliskan perkembangan/pengerjaan yang dilakukan..." required></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Progress
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
