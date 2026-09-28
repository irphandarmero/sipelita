@extends('layouts.app')

@section('title', 'Manajemen Pengguna (User)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-people text-primary me-2"></i>Manajemen Pengguna (User)</h4>
        <p class="text-muted mb-0 small">Kelola akun staf, kepala unit, dan administrator aplikasi SI-PELITA.</p>
    </div>
    <button type="button" class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
        <i class="bi bi-plus-lg me-1"></i> Tambah Pengguna
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

<!-- Tabel Users -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 80px;">No</th>
                        <th>Nama Pengguna</th>
                        <th>Alamat Email</th>
                        <th>Unit Kerja</th>
                        <th>Role Akses</th>
                        <th class="pe-3 text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usersList as $index => $user)
                        <tr>
                            <td class="ps-3 text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark">{{ $user->name }}</td>
                            <td><span class="text-muted">{{ $user->email }}</span></td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                    {{ $user->unitKerja->nama_unit ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if($user->role == 'admin')
                                    <span class="badge bg-danger">Administrator</span>
                                @elseif($user->role == 'kepala_unit')
                                    <span class="badge bg-primary">Kepala Unit</span>
                                @else
                                    <span class="badge bg-info text-dark">Petugas / Staf</span>
                                @endif
                            </td>
                            <td class="pe-3 text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#modalEditUser{{ $user->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('master.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit User -->
                        <div class="modal fade" id="modalEditUser{{ $user->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('master.users.update', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-primary text-white">
                                            <h6 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Pengguna</h6>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nama Lengkap</label>
                                                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Alamat Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Unit Kerja</label>
                                                <select name="unit_kerja_id" class="form-select" required>
                                                    @foreach($unitKerjaList as $unit)
                                                        <option value="{{ $unit->id }}" {{ $user->unit_kerja_id == $unit->id ? 'selected' : '' }}>
                                                            {{ $unit->nama_unit }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Role Akses</label>
                                                <select name="role" class="form-select" required>
                                                    <option value="petugas" {{ $user->role == 'petugas' ? 'selected' : '' }}>Petugas / Staf Penangan</option>
                                                    <option value="kepala_unit" {{ $user->role == 'kepala_unit' ? 'selected' : '' }}>Kepala Unit / Pelapor</option>
                                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator Sistem</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Ubah Password <small class="text-muted fw-normal">(Kosongkan jika tidak diubah)</small></label>
                                                <input type="password" name="password" class="form-control" placeholder="******">
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
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data pengguna yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="modalTambahUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('master.users.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h6 class="modal-title fw-bold"><i class="bi bi-plus-lg me-2"></i>Tambah Pengguna Baru</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama & Gelar Staf" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Email</label>
                        <input type="email" name="email" class="form-control" placeholder="staf@sipelita.id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Unit Kerja</label>
                        <select name="unit_kerja_id" class="form-select" required>
                            <option value="">-- Pilih Unit Kerja --</option>
                            @foreach($unitKerjaList as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role Akses</label>
                        <select name="role" class="form-select" required>
                            <option value="petugas">Petugas / Staf Penangan</option>
                            <option value="kepala_unit">Kepala Unit / Pelapor</option>
                            <option value="admin">Administrator Sistem</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="******" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-semibold">Tambah User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
