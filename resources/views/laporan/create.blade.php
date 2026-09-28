@extends('layouts.app')

@section('title', 'Buat Laporan Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Header Page -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-plus-circle text-primary me-2"></i>Buat Laporan / Aduan Baru
                </h4>
                <p class="text-muted mb-0 small">
                    Isi formulir di bawah ini untuk mengirimkan laporan rutin atau aduan insidentil antar-unit RS.
                </p>
            </div>
            <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>

        <!-- Form Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- 1. INFORMASI PENGIRIM (OTOMATIS SAKSI UNITT) -->
                    <div class="alert alert-primary bg-primary bg-opacity-10 border-0 d-flex align-items-center p-3 mb-4">
                        <i class="bi bi-info-circle-fill text-primary fs-4 me-3"></i>
                        <div class="small text-dark">
                            Laporan ini dikirim atas nama: <strong>{{ Auth::user()->name }}</strong> dari Unit Kerja 
                            <span class="badge bg-primary ms-1">{{ Auth::user()->unitKerja->nama_unit ?? 'Unit Kerja' }}</span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- 2. KATEGORI LAPORAN (Konek ke Model MasterLaporan) -->
                        <div class="col-md-8">
                            <label for="master_laporan_id" class="form-label fw-bold small text-dark">
                                Kategori Laporan <span class="text-danger">*</span>
                            </label>
                            <select name="master_laporan_id" id="master_laporan_id" 
                                    class="form-select @error('master_laporan_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori Laporan --</option>
                                @foreach($masterLaporanList as $kategori)
                                    <option value="{{ $kategori->id }}" {{ old('master_laporan_id') == $kategori->id ? 'selected' : '' }}>
                                        [{{ $kategori->jenis_laporan }}] {{ $kategori->nama_laporan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('master_laporan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">Pilih kategori yang paling menggambarkan jenis laporan.</small>
                        </div>

                        <!-- 3. UNIT TUJUAN PENANGGUNG JAWAB (Konek ke Model UnitKerja) -->
                        <div class="col-md-8">
                            <label for="unit_tujuan_id" class="form-label fw-bold small text-dark">
                                Unit Kerja Tujuan (Penanggung Jawab) <span class="text-danger">*</span>
                            </label>
                            <select name="unit_tujuan_id" id="unit_tujuan_id" 
                                    class="form-select @error('unit_tujuan_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Unit Tujuan --</option>
                                @foreach($unitKerjaList as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_tujuan_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->nama_unit }} ({{ $unit->kode_unit }})
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_tujuan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">Pilih unit yang bertugas menangani masalah ini (misal: SIMRS / IPSRS / Direksi).</small>
                        </div>

                        <!-- 4. JUDUL LAPORAN -->
                        <div class="col-md-8">
                            <label for="judul_laporan" class="form-label fw-bold small text-dark">
                                Judul Laporan / Ringkasan Masalah <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="judul_laporan" id="judul_laporan" 
                                   class="form-control @error('judul_laporan') is-invalid @enderror" 
                                   value="{{ old('judul_laporan') }}" 
                                   placeholder="Contoh: PC Triase IGD Tidak Bisa Cetak Struk Pasien" required>
                            @error('judul_laporan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 5. TINGKAT PRIORITAS -->
                        <div class="col-md-4">
                            <label for="prioritas" class="form-label fw-bold small text-dark">
                                Tingkat Prioritas <span class="text-danger">*</span>
                            </label>
                            <select name="prioritas" id="prioritas" 
                                    class="form-select @error('prioritas') is-invalid @enderror" required>
                                <option value="Rendah" {{ old('prioritas') == 'Rendah' ? 'selected' : '' }}>Rendah (Rutinitas biasa)</option>
                                <option value="Sedang" {{ old('prioritas', 'Sedang') == 'Sedang' ? 'selected' : '' }}>Sedang (Perlu penanganan standar)</option>
                                <option value="Tinggi" {{ old('prioritas') == 'Tinggi' ? 'selected' : '' }}>Tinggi (Mempengaruhi pelayanan)</option>
                                <option value="Darurat" {{ old('prioritas') == 'Darurat' ? 'selected' : '' }}>Darurat 🔥 (Layanan kritis terhenti)</option>
                            </select>
                            @error('prioritas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 6. TANGGAL KEJADIAN -->
                        <div class="col-md-6">
                            <label for="tanggal_kejadian" class="form-label fw-bold small text-dark">
                                Tanggal & Waktu Kejadian <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" name="tanggal_kejadian" id="tanggal_kejadian" 
                                   class="form-control @error('tanggal_kejadian') is-invalid @enderror" 
                                   value="{{ old('tanggal_kejadian', date('Y-m-d\TH:i')) }}" required>
                            @error('tanggal_kejadian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 7. UPLOAD LAMPIRAN / BUKTI FOTO -->
                        <div class="col-md-6">
                            <label for="lampiran" class="form-label fw-bold small text-dark">
                                Lampiran Bukti / Foto Kerusakan <small class="text-muted fw-normal">(Opsional)</small>
                            </label>
                            <input type="file" name="lampiran" id="lampiran" 
                                   class="form-control @error('lampiran') is-invalid @enderror" 
                                   accept="image/*,.pdf,.doc,.docx">
                            @error('lampiran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">Format: JPG, PNG, PDF (Maks. 2MB).</small>
                        </div>

                        <!-- 8. DESKRIPSI KEJADIAN / RINCIAN -->
                        <div class="col-12">
                            <label for="deskripsi_kejadian" class="form-label fw-bold small text-dark">
                                Rincian Kejadian / Isi Laporan <span class="text-danger">*</span>
                            </label>
                            <textarea name="deskripsi_kejadian" id="deskripsi_kejadian" rows="4" 
                                      class="form-control @error('deskripsi_kejadian') is-invalid @enderror" 
                                      placeholder="Jelaskan secara rinci kondisi masalah, lokasi tepatnya, atau data sensus yang dilaporkan..." required>{{ old('deskripsi_kejadian') }}</textarea>
                            @error('deskripsi_kejadian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 9. TINDAKAN AWAL SEBELUMNYA -->
                        <div class="col-12">
                            <label for="tindakan_awal" class="form-label fw-bold small text-dark">
                                Tindakan Awal Yang Sudah Dilakukan <small class="text-muted fw-normal">(Opsional)</small>
                            </label>
                            <textarea name="tindakan_awal" id="tindakan_awal" rows="2" 
                                      class="form-control @error('tindakan_awal') is-invalid @enderror" 
                                      placeholder="Contoh: Sudah dicoba restart PC dan mengecek sambungan kabel power...">{{ old('tindakan_awal') }}</textarea>
                            @error('tindakan_awal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Button Footer -->
                    <hr class="my-4">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('laporan.index') }}" class="btn btn-light border px-4 fw-semibold">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="bi bi-send-fill me-2"></i>Kirim Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
