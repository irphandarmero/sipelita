@extends('layouts.app')

@section('title', 'Master Unit Kerja')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-building text-primary me-2"></i>Master Unit Kerja RS</h4>
        <p class="text-muted mb-0 small">Kelola daftar unit/instalasi di Rumah Sakit yang terhubung dengan laporan & pengguna.</p>
    </div>
    <button type="button" class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahUnit">
        <i class="bi bi-plus-lg me-1"></i> Tambah Unit Kerja
    </button>
</div>

<!-- Alert Notifikasi -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Tabel Unit Kerja -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 80px;">No</th>
                        <th>Kode Unit</th>
                        <th>Nama Unit Kerja</th>
                        <th class="text-center">Jumlah Staf</th>
                        <th class="text-center">Total Laporan</th>
                        <th class="pe-3 text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($unitKerjaList as $index => $unit)
                        <tr>
                            <td class="ps-3 text-muted">{{ $index + 1 }}</td>
                            <td><span class="badge bg-secondary font-monospace fs-6">{{ $unit->kode_unit }}</span></td>
                            <td class="fw-bold text-dark">{{ $unit->nama_unit }}</td>
                            <td class="text-center"><span class="badge bg-info text-dark">{{ $unit->users_count }} Orang</span></td>
                            <td class="text-center"><span class="badge bg-light text-dark border">{{ $unit->laporan_asal_count + $unit->laporan_tujuan_count }} Laporan</span></td>
                            <td class="pe-3 text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#modalEditUnit{{ $unit->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('master.unit-kerja.destroy', $unit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit kerja ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit Unit Kerja -->
                        <div class="modal fade" id="modalEditUnit{{ $unit->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('master.unit-kerja.update', $unit->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-primary text-white">
                                            <h6 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Unit Kerja</h6>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Kode Unit Kerja</label>
                                                <input type="text" name="kode_unit" class="form-control" value="{{ $unit->kode_unit }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nama Unit Kerja</label>
                                                <input type="text" name="nama_unit" class="form-control" value="{{ $unit->nama_unit }}" required>
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
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data unit kerja yang tersimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Unit Kerja -->
<div class="modal fade" id="modalTambahUnit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('master.unit-kerja.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h6 class="modal-title fw-bold"><i class="bi bi-plus-lg me-2"></i>Tambah Unit Kerja Baru</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Unit (Contoh: UNIT-IGD)</label>
                        <input type="text" name="kode_unit" class="form-control" placeholder="UNIT-XXX" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Unit Kerja</label>
                        <input type="text" name="nama_unit" class="form-control" placeholder="Misal: Instalasi Gawat Darurat" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-semibold">Tambah Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
