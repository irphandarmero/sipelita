@extends('layouts.app')

@section('title', 'Master Kategori Laporan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-journal-bookmark text-primary me-2"></i>Master Kategori Laporan</h4>
        <p class="text-muted mb-0 small">Atur kategori laporan rutin (sensus/survei) dan insidentil (aduan/kerusakan) di SI-PELITA.</p>
    </div>
    <button type="button" class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahMaster">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
    </button>
</div>

<!-- Alert Notifikasi -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Tabel Master Laporan -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 80px;">No</th>
                        <th>Nama Kategori Laporan</th>
                        <th>Jenis Laporan</th>
                        <th>Deskripsi Single Line</th>
                        <th class="text-center">Status Aktif</th>
                        <th class="pe-3 text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($masterLaporanList as $index => $master)
                        <tr>
                            <td class="ps-3 text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark">{{ $master->nama_laporan }}</td>
                            <td>
                                @if($master->jenis_laporan == 'Rutin')
                                    <span class="badge bg-info text-dark">Rutin</span>
                                @else
                                    <span class="badge bg-warning text-dark">Insidentil</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $master->deskripsi ?? '-' }}</td>
                            <td class="text-center">
                                @if($master->status_aktif)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="pe-3 text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#modalEditMaster{{ $master->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('master.master-laporan.destroy', $master->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus/menonaktifkan kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit Master Laporan -->
                        <div class="modal fade" id="modalEditMaster{{ $master->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('master.master-laporan.update', $master->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-primary text-white">
                                            <h6 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Kategori Laporan</h6>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nama Kategori Laporan</label>
                                                <input type="text" name="nama_laporan" class="form-control" value="{{ $master->nama_laporan }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Jenis Laporan</label>
                                                <select name="jenis_laporan" class="form-select" required>
                                                    <option value="Rutin" {{ $master->jenis_laporan == 'Rutin' ? 'selected' : '' }}>Rutin (Sensus/Survei Periodic)</option>
                                                    <option value="Insidentil" {{ $master->jenis_laporan == 'Insidentil' ? 'selected' : '' }}>Insidentil (Aduan / Kerusakan Sarpras)</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Deskripsi Singkat</label>
                                                <textarea name="deskripsi" class="form-control" rows="2">{{ $master->deskripsi }}</textarea>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" name="status_aktif" id="status_aktif_{{ $master->id }}" {{ $master->status_aktif ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="status_aktif_{{ $master->id }}">Status Kategori Aktif</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary btn-sm fw-semibold">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data master kategori laporan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Master Laporan -->
<div class="modal fade" id="modalTambahMaster" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('master.master-laporan.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h6 class="modal-title fw-bold"><i class="bi bi-plus-lg me-2"></i>Tambah Kategori Laporan</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori Laporan</label>
                        <input type="text" name="nama_laporan" class="form-control" placeholder="Misal: Kerusakan Hardware & SIMRS" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Laporan</label>
                        <select name="jenis_laporan" class="form-select" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Rutin">Rutin (Sensus/Survei Periodic)</option>
                            <option value="Insidentil">Insidentil (Aduan / Kerusakan Sarpras)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Singkat</label>
                        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Penjelasan singkat peruntukan kategori..."></textarea>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="status_aktif" id="status_aktif_add" checked>
                        <label class="form-check-label fw-semibold" for="status_aktif_add">Status Kategori Aktif</label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-semibold">Tambah Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
