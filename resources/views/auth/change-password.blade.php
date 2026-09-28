@extends('layouts.app')

@section('title', 'Ubah Password')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">
            <i class="bi bi-key-fill text-warning me-2"></i>Ubah Password Aku
        </h4>
        <p class="text-muted small mb-0">Perbarui kata sandi akun SI-PELITA Anda secara berkala untuk menjaga keamanan data.</p>
    </div>

    <div class="row">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark">Formulir Pembaruan Kata Sandi</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf

                        <!-- Password Saat Ini -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold small">
                                Password Saat Ini <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                <input type="password" name="current_password" id="current_password" 
                                       class="form-control border-start-0 @error('current_password') is-invalid @enderror" 
                                       placeholder="Masukkan password saat ini" required>
                                @error('current_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Baru -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold small">
                                Password Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-lock text-muted"></i></span>
                                <input type="password" name="password" id="password" 
                                       class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                       placeholder="Minimal 8 karakter" required>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">Password baru harus minimal 8 karakter dan berbeda dari password lama.</small>
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold small">
                                Konfirmasi Password Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-check text-muted"></i></span>
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                       class="form-control border-start-0" 
                                       placeholder="Ulangi password baru" required>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-warning text-dark fw-semibold px-4">
                                <i class="bi bi-check-circle me-1"></i> Simpan Password Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Tips Keamanan -->
        <div class="col-12 col-md-4 mt-4 mt-md-0">
            <div class="card border-0 shadow-sm bg-warning bg-opacity-10 text-dark">
                <div class="card-body">
                    <h6 class="fw-bold"><i class="bi bi-shield-shaded me-2 text-warning"></i>Tips Keamanan Password</h6>
                    <ul class="ps-3 mb-0 small">
                        <li class="mb-2">Gunakan kombinasi minimal 8 karakter dengan huruf besar, huruf kecil, dan angka.</li>
                        <li class="mb-2">Jangan gunakan password yang mudah ditebak seperti tanggal lahir atau kata 'password123'.</li>
                        <li class="mb-2">Ganti password secara berkala untuk menjaga kerahasiaan akun dan data pelaporan rumah sakit.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
