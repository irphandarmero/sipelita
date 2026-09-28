@extends('layouts.app')

@section('title', 'Daftar Laporan & Aduan')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi bi-file-earmark-text text-primary me-2"></i>Daftar Laporan & Aduan RS
        </h4>
        <p class="text-muted mb-0 small">
            Kelola dan pantau seluruh laporan rutin serta aduan insidentil antar-unit di SI-PELITA.
        </p>
    </div>
    <div>
        <a href="{{ route('laporan.create') }}" class="btn btn-primary fw-semibold px-3 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Buat Laporan Baru
        </a>
    </div>
</div>

<!-- ========================================== -->
<!-- FILTER & SEARCH BAR (Konek ke Controller & Model) -->
<!-- ========================================== -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('laporan.index') }}" method="GET" class="row g-2">
            <!-- Parameter View Type (jika ada) -->
            @if(request('view_type'))
                <input type="hidden" name="view_type" value="{{ request('view_type') }}">
            @endif

            <!-- Pencarian Kata Kunci -->
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-select-sm form-control border-start-0" 
                           placeholder="Cari Kode TRX / Judul / Pelapor..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- Filter Jenis Laporan -->
            <div class="col-md-2 col-6">
                <select name="jenis" class="form-select form-select-sm">
                    <option value="">-- Semua Jenis --</option>
                    <option value="Rutin" {{ request('jenis') == 'Rutin' ? 'selected' : '' }}>Rutin (Sensus/Survei)</option>
                    <option value="Insidentil" {{ request('jenis') == 'Insidentil' ? 'selected' : '' }}>Insidentil (Kerusakan/Aduan)</option>
                </select>
            </div>

            <!-- Filter Status Laporan -->
            <div class="col-md-2 col-6">
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="Diajukan" {{ request('status') == 'Diajukan' ? 'selected' : '' }}>Diajukan 🟡</option>
                    <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses 🔵</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai 🟢</option>
                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak 🔴</option>
                </select>
            </div>

            <!-- Filter Prioritas Kedaruratan -->
            <div class="col-md-2 col-6">
                <select name="prioritas" class="form-select form-select-sm">
                    <option value="">-- Semua Prioritas --</option>
                    <option value="Rendah" {{ request('prioritas') == 'Rendah' ? 'selected' : '' }}>Rendah</option>
                    <option value="Sedang" {{ request('prioritas') == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="Tinggi" {{ request('prioritas') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                    <option value="Darurat" {{ request('prioritas') == 'Darurat' ? 'selected' : '' }}>Darurat 🔥</option>
                </select>
            </div>

            <!-- Filter Unit Tujuan (Konek Data Model UnitKerja) -->
            <div class="col-md-2 col-6">
                <select name="unit_tujuan_id" class="form-select form-select-sm">
                    <option value="">-- Semua Unit Tujuan --</option>
                    @foreach($unitKerjaList as $unit)
                        <option value="{{ $unit->id }}" {{ request('unit_tujuan_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->nama_unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Aksi Filter & Reset -->
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100" title="Terapkan Filter">
                    <i class="bi bi-filter"></i>
                </button>
                <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-light border w-100" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- TABEL DAFTAR LAPORAN (Konek ke DetailLaporan) -->
<!-- ========================================== -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 140px;">Kode Transaksi</th>
                        <th style="width: 150px;">Tanggal & Waktu</th>
                        <th>Judul & Kategori Laporan</th>
                        <th>Unit (Asal ➔ Tujuan)</th>
                        <th style="width: 120px;">Pelapor</th>
                        <th style="width: 100px;">Prioritas</th>
                        <th style="width: 110px;">Status</th>
                        <th class="pe-3 text-center" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporanList as $laporan)
                        <tr>
                            <!-- 1. Kode Transaksi -->
                            <td class="ps-3 fw-bold text-primary">
                                {{ $laporan->kode_transaksi }}
                            </td>

                            <!-- 2. Tanggal Kejadian -->
                            <td class="text-muted">
                                <i class="bi bi-calendar-event me-1"></i>
                                {{ $laporan->tanggal_kejadian ? $laporan->tanggal_kejadian->format('d/m/Y H:i') : '-' }}
                            </td>

                            <!-- 3. Judul & Kategori (Relasi: $laporan->masterLaporan) -->
                            <td>
                                <div class="fw-semibold text-dark">{{ $laporan->judul_laporan }}</div>
                                <small class="text-muted">
                                    <span class="badge bg-light text-dark border me-1">
                                        {{ $laporan->masterLaporan->jenis_laporan ?? 'N/A' }}
                                    </span>
                                    {{ $laporan->masterLaporan->nama_laporan ?? 'Kategori tidak ditemukan' }}
                                </small>
                            </td>

                            <!-- 4. Unit Asal ➔ Unit Tujuan (Relasi: $laporan->unitAsal & $laporan->unitTujuan) -->
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                    {{ $laporan->unitAsal->nama_unit ?? 'N/A' }}
                                </span>
                                <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                    {{ $laporan->unitTujuan->nama_unit ?? 'N/A' }}
                                </span>
                            </td>

                            <!-- 5. Nama Pelapor (Relasi: $laporan->user) -->
                            <td>
                                <div class="fw-medium">{{ $laporan->user->name ?? 'Anonim' }}</div>
                            </td>

                            <!-- 6. Badge Prioritas -->
                            <td>
                                @if($laporan->prioritas == 'Darurat')
                                    <span class="badge bg-danger text-white px-2 py-1"><i class="bi bi-exclamation-octagon-fill me-1"></i>Darurat</span>
                                @elseif($laporan->prioritas == 'Tinggi')
                                    <span class="badge bg-warning text-dark px-2 py-1">Tinggi</span>
                                @elseif($laporan->prioritas == 'Sedang')
                                    <span class="badge bg-info text-dark px-2 py-1">Sedang</span>
                                @else
                                    <span class="badge bg-secondary px-2 py-1">Rendah</span>
                                @endif
                            </td>

                            <!-- 7. Badge Status Laporan -->
                            <td>
                                @if($laporan->status_laporan == 'Diajukan')
                                    <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-hourglass-split me-1"></i>Diajukan</span>
                                @elseif($laporan->status_laporan == 'Diproses')
                                    <span class="badge bg-info text-dark px-2 py-1"><i class="bi bi-gear-wide-connected me-1"></i>Diproses</span>
                                @elseif($laporan->status_laporan == 'Selesai')
                                    <span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i>Selesai</span>
                                @elseif($laporan->status_laporan == 'Ditolak')
                                    <span class="badge bg-danger px-2 py-1"><i class="bi bi-x-circle-fill me-1"></i>Ditolak</span>
                                @else
                                    <span class="badge bg-light text-dark border">{{ $laporan->status_laporan }}</span>
                                @endif
                            </td>

                            <!-- 8. Tombol Aksi -->
                            <td class="pe-3 text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <!-- Tombol Detail -->
                                    <a href="{{ route('laporan.show', $laporan->id) }}" class="btn btn-outline-primary" title="Lihat Detail & Timeline">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <!-- Tombol Update Status via Modal (Khusus Petugas/Admin/Unit Tujuan) -->
                                    @if(in_array(Auth::user()->role ?? '', ['admin', 'petugas', 'kepala_unit']))
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalStatus{{ $laporan->id }}" title="Tindak Lanjut Status">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Belum ada data laporan yang sesuai dengan kriteria pencarian/filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Footer -->
    @if(method_exists($laporanList, 'links'))
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $laporanList->firstItem() ?? 0 }} - {{ $laporanList->lastItem() ?? 0 }} dari {{ $laporanList->total() ?? 0 }} data
                </small>
                <div>
                    {{ $laporanList->withQueryString()->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

<!-- ========================================== -->
<!-- MODAL DIALOG UPDATE STATUS (Per Baris Data) -->
<!-- ========================================== -->
@foreach($laporanList as $laporan)
    @if(in_array(Auth::user()->role ?? '', ['admin', 'petugas', 'kepala_unit']))
        <div class="modal fade" id="modalStatus{{ $laporan->id }}" tabindex="-1" aria-labelledby="modalStatusLabel{{ $laporan->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('laporan.updateStatus', $laporan->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="modal-header bg-primary text-white">
                            <h6 class="modal-title fw-bold" id="modalStatusLabel{{ $laporan->id }}">
                                <i class="bi bi-pencil-square me-2"></i>Tindak Lanjut Laporan #{{ $laporan->kode_transaksi }}
                            </h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        
                        <div class="modal-body p-4">
                            <!-- Ringkasan Info Aduan -->
                            <div class="p-3 bg-light rounded mb-3 border">
                                <div class="fw-bold text-dark mb-1">{{ $laporan->judul_laporan }}</div>
                                <div class="small text-muted mb-2">
                                    Pelapor: <strong>{{ $laporan->user->name ?? 'Anonim' }}</strong> ({{ $laporan->unitAsal->nama_unit ?? '-' }})
                                </div>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-secondary">Status Sekarang: {{ $laporan->status_laporan }}</span>
                                    <span class="badge bg-info text-dark">Prioritas: {{ $laporan->prioritas }}</span>
                                </div>
                            </div>

                            <!-- Form Pilihan Status Baru -->
                            <div class="mb-3">
                                <label for="status_baru_{{ $laporan->id }}" class="form-label fw-semibold">
                                    Pilih Status Baru <span class="text-danger">*</span>
                                </label>
                                <select name="status_baru" id="status_baru_{{ $laporan->id }}" class="form-select" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Diproses" {{ $laporan->status_laporan == 'Diproses' ? 'selected' : '' }}>
                                        Diproses (Teknisi sedang menangani)
                                    </option>
                                    <option value="Selesai" {{ $laporan->status_laporan == 'Selesai' ? 'selected' : '' }}>
                                        Selesai (Kendala/Tugas telah tuntas)
                                    </option>
                                    <option value="Ditolak" {{ $laporan->status_laporan == 'Ditolak' ? 'selected' : '' }}>
                                        Ditolak (Bukan wewenang / Laporan tidak valid)
                                    </option>
                                </select>
                            </div>

                            <!-- Input Catatan Progress / Audit Trail -->
                            <div class="mb-3">
                                <label for="keterangan_{{ $laporan->id }}" class="form-label fw-semibold">
                                    Catatan Progress / Keterangan Tindakan <span class="text-danger">*</span>
                                </label>
                                <textarea name="keterangan" id="keterangan_{{ $laporan->id }}" rows="3" 
                                          class="form-control" placeholder="Tuliskan tindakan yang dilakukan (misal: Printer cadangan telah dipasang di IGD)..." required></textarea>
                                <small class="text-muted d-block mt-1">
                                    Catatan ini akan tersimpan otomatis di halaman <strong>Timeline/Riwayat Laporan</strong>.
                                </small>
                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm fw-semibold">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection
