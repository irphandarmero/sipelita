@extends('layouts.app')

@section('title', $isEditing ? 'Edit Laporan' : 'Buat Laporan Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Header Page -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-plus-circle text-primary me-2"></i>{{ $isEditing ? 'Edit Laporan / Aduan' : 'Buat Laporan / Aduan Baru' }}
                </h4>
                <p class="text-muted mb-0 small">
                    {{ $isEditing ? 'Perbarui isi laporan sebelum ditinjau atau diproses.' : 'Isi formulir di bawah ini untuk mengirimkan laporan rutin atau aduan insidentil antar-unit RS.' }}
                </p>
            </div>
            <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>

        <!-- Form Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ $isEditing ? route('laporan.update', $laporan->id) : route('laporan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if($isEditing)
                        @method('PUT')
                    @endif

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
                                <option value="" {{ old('master_laporan_id', $isEditing ? $laporan->master_laporan_id : '') == '' ? 'selected' : '' }}>-- Pilih Kategori Laporan --</option>
                                @foreach($masterLaporanList as $kategori)
                                    <option value="{{ $kategori->id }}" data-jenis="{{ $kategori->jenis_laporan }}" {{ old('master_laporan_id', $isEditing ? $laporan->master_laporan_id : '') == $kategori->id ? 'selected' : '' }}>
                                        [{{ $kategori->jenis_laporan }}] {{ $kategori->nama_laporan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('master_laporan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <!-- <small class="text-muted d-block mt-1">Pilih kategori yang paling menggambarkan jenis laporan.</small> -->
                        </div>

                        <div class="col-12" id="incidentil-guidance" hidden>
                            <div class="alert alert-info mb-0">
                                Untuk laporan Insidentil, jelaskan kronologi, lokasi, dampak kejadian, dan tindakan awal yang sudah dilakukan.
                            </div>
                        </div>

                        <!-- 3. UNIT TUJUAN PENANGGUNG JAWAB (Konek ke Model UnitKerja) -->
                        <div class="col-md-8">
                            <label for="unit_tujuan_id" class="form-label fw-bold small text-dark">
                                Unit Kerja Tujuan (Penanggung Jawab) <span class="text-danger">*</span>
                            </label>
                            <select name="unit_tujuan_id" id="unit_tujuan_id" 
                                    class="form-select @error('unit_tujuan_id') is-invalid @enderror" required>
                                <option value="" {{ old('unit_tujuan_id', $isEditing ? $laporan->unit_tujuan_id : '') == '' ? 'selected' : '' }}>-- Pilih Unit Tujuan --</option>
                                @foreach($unitKerjaList as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_tujuan_id', $isEditing ? $laporan->unit_tujuan_id : '') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->nama_unit }} ({{ $unit->kode_unit }})
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_tujuan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <!-- <small class="text-muted d-block mt-1">Pilih unit yang bertugas menangani masalah ini (misal: SIMRS / IPSRS / Direksi).</small> -->
                        </div>

                        <!-- 4. JUDUL LAPORAN -->
                        <div class="col-md-8">
                            <label for="judul_laporan" class="form-label fw-bold small text-dark">
                                Judul Laporan / Ringkasan Masalah <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="judul_laporan" id="judul_laporan" 
                                   class="form-control @error('judul_laporan') is-invalid @enderror" 
                                   value="{{ old('judul_laporan', $isEditing ? $laporan->judul_laporan : '') }}"
                                   required>
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
                                <option value="Rendah" {{ old('prioritas', $isEditing ? $laporan->prioritas : '') == 'Rendah' ? 'selected' : '' }}>Rendah (Rutinitas biasa)</option>
                                <option value="Sedang" {{ old('prioritas', $isEditing ? $laporan->prioritas : 'Sedang') == 'Sedang' ? 'selected' : '' }}>Sedang (Perlu penanganan standar)</option>
                                <option value="Tinggi" {{ old('prioritas', $isEditing ? $laporan->prioritas : '') == 'Tinggi' ? 'selected' : '' }}>Tinggi (Mempengaruhi pelayanan)</option>
                                <option value="Darurat" {{ old('prioritas', $isEditing ? $laporan->prioritas : '') == 'Darurat' ? 'selected' : '' }}>Darurat 🔥 (Layanan kritis terhenti)</option>
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
                                   value="{{ old('tanggal_kejadian', $isEditing ? $laporan->tanggal_kejadian->format('Y-m-d\TH:i') : date('Y-m-d\TH:i')) }}" required>
                            @error('tanggal_kejadian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 7. DESKRIPSI KEJADIAN / RINCIAN -->
                        <div class="col-12">
                            <label for="deskripsi_kejadian" class="form-label fw-bold small text-dark">
                                Rincian Kejadian / Isi Laporan <span class="text-danger">*</span>
                            </label>
                            <textarea name="deskripsi_kejadian" id="deskripsi_kejadian" rows="4" 
                                      class="form-control @error('deskripsi_kejadian') is-invalid @enderror" 
                                      required>{{ old('deskripsi_kejadian', $isEditing ? $laporan->deskripsi_kejadian : '') }}</textarea>
                            @error('deskripsi_kejadian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 8. UPLOAD LAMPIRAN / BUKTI -->
                        <div class="col-md-6">
                            <label for="lampiran" class="form-label fw-bold small text-dark">
                                Lampiran Bukti / Foto / Dokumen <small class="text-muted fw-normal">(Opsional)</small>
                            </label>
                            <input type="file" name="lampiran_file" id="lampiran"
                                   class="form-control @error('lampiran_file') is-invalid @enderror"
                                   accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf">
                            @if($isEditing && $laporan->lampiran_file)
                                <small class="text-muted d-block mt-1">Lampiran saat ini: <a href="{{ asset('storage/' . $laporan->lampiran_file) }}" target="_blank" rel="noopener">Lihat lampiran</a>. Kosongkan jika tidak ingin menggantinya.</small>
                            @endif
                            @error('lampiran_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">Format: JPG, JPEG, PNG, atau PDF (maksimal 5MB).</small>
                        </div>

                        <!-- 9. TINDAKAN AWAL SEBELUMNYA -->
                        <div class="col-12" id="tindakan-awal-field" hidden>
                            <label for="tindakan_awal" class="form-label fw-bold small text-dark">
                                Tindakan Awal Yang Sudah Dilakukan <span class="text-danger">*</span>
                            </label>
                            <textarea name="tindakan_awal" id="tindakan_awal" rows="2" 
                                      class="form-control @error('tindakan_awal') is-invalid @enderror" 
                                      placeholder="Jelaskan tindakan yang telah dilakukan sebelum laporan dikirim...">{{ old('tindakan_awal', $isEditing ? $laporan->tindakan_awal : '') }}</textarea>
                            @error('tindakan_awal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Button Footer -->
                    <hr class="my-4">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ $isEditing ? route('laporan.show', $laporan->id) : route('laporan.index') }}" class="btn btn-light border px-4 fw-semibold">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="bi {{ $isEditing ? 'bi-save' : 'bi-send-fill' }} me-2"></i>{{ $isEditing ? 'Simpan Perubahan' : 'Kirim Laporan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const kategoriSelect = document.getElementById('master_laporan_id');
        const tindakanAwalField = document.getElementById('tindakan-awal-field');
        const tindakanAwalInput = document.getElementById('tindakan_awal');
        const incidentilGuidance = document.getElementById('incidentil-guidance');

        function updateIncidentilFields() {
            const selectedOption = kategoriSelect.options[kategoriSelect.selectedIndex];
            const isIncidentil = selectedOption && selectedOption.dataset.jenis === 'Insidentil';

            tindakanAwalField.hidden = !isIncidentil;
            incidentilGuidance.hidden = !isIncidentil;
            tindakanAwalInput.required = Boolean(isIncidentil);
        }

        kategoriSelect.addEventListener('change', updateIncidentilFields);
        updateIncidentilFields();
    });
</script>
@endpush
@endsection
