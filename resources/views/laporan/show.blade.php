@extends('layouts.app')

@section('title', 'Detail Laporan - ' . $laporan->kode_transaksi)

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb & Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Daftar Laporan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $laporan->kode_transaksi }}</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0">{{ $laporan->judul_laporan }}</h4>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
            
            <!-- Tombol Disposisi khusus Unit Tujuan / Admin / Petugas -->
            @if(Auth::user()->unit_kerja_id == $laporan->unit_tujuan_id || Auth::user()->role == 'admin' || Auth::user()->id == $laporan->petugas_penangan_id)
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalKirimDisposisi">
                    <i class="bi bi-send-plus me-1"></i> Kirim Disposisi
                </button>
            @endif

            <!-- Tombol Update Status Utama -->
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalUpdateStatus">
                <i class="bi bi-pencil-square me-1"></i> Update Status
            </button>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Detail Card & Disposisi Cards -->
        <div class="col-12 col-lg-8">
            <!-- Card Detail Utama -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-file-text me-2 text-primary"></i>Rincian Transaksi Aduan</h6>
                    @php
                        $badgePrioritas = [
                            'Rendah' => 'bg-secondary',
                            'Sedang' => 'bg-info text-dark',
                            'Tinggi' => 'bg-warning text-dark',
                            'Darurat' => 'bg-danger',
                        ][$laporan->prioritas] ?? 'bg-primary';
                    @endphp
                    <span class="badge {{ $badgePrioritas }} fs-7">{{ $laporan->prioritas }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Kode Transaksi</small>
                            <span class="fw-bold text-primary">{{ $laporan->kode_transaksi }}</span>
                        </div>
                        <div class="col-6 col-md-3">
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Tanggal Kejadian</small>
                            <span class="fw-semibold">{{ $laporan->tanggal_kejadian->format('d M Y, H:i') }} WIB</span>
                        </div>
                        <div class="col-6 col-md-3">
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Jenis Laporan</small>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                {{ $laporan->masterLaporan->jenis_laporan ?? 'Insidentil' }}
                            </span>
                        </div>
                        <div class="col-6 col-md-3">
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Status Laporan</small>
                            @php
                                $badgeStatus = [
                                    'Diajukan' => 'bg-warning text-dark',
                                    'Diproses' => 'bg-info text-dark',
                                    'Selesai'  => 'bg-success',
                                    'Ditolak'  => 'bg-danger',
                                ][$laporan->status_laporan] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $badgeStatus }}">{{ $laporan->status_laporan }}</span>
                        </div>
                    </div>

                    <div class="row g-3 mb-4 p-3 bg-light rounded-3 border">
                        <div class="col-12 col-md-6">
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Unit Asal (Pelapor)</small>
                            <div class="fw-bold text-dark"><i class="bi bi-building me-1 text-secondary"></i>{{ $laporan->unitAsal->nama_unit ?? '-' }}</div>
                            <small class="text-muted">Pelapor: {{ $laporan->user->name ?? '-' }}</small>
                        </div>
                        <div class="col-12 col-md-6">
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Unit Tujuan (Penanggung Jawab)</small>
                            <div class="fw-bold text-primary"><i class="bi bi-tools me-1"></i>{{ $laporan->unitTujuan->nama_unit ?? '-' }}</div>
                            <small class="text-muted">Petugas: {{ $laporan->petugasPenangan->name ?? 'Belum Ditentukan' }}</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark small mb-2">Deskripsi Kejadian / Rincian:</h6>
                        <p class="text-secondary bg-white p-3 rounded border mb-0">{{ $laporan->deskripsi_kejadian }}</p>
                    </div>

                    @if($laporan->tindakan_awal)
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark small mb-2">Tindakan Awal yang Sudah Dilakukan:</h6>
                            <p class="text-secondary bg-white p-3 rounded border mb-0">{{ $laporan->tindakan_awal }}</p>
                        </div>
                    @endif

                    @if($laporan->lampiran_file)
                        <div class="card bg-light border p-3">
                            <h6 class="fw-bold mb-2"><i class="bi bi-paperclip me-1"></i>Lampiran Berkas Laporan:</h6>
                            @php
                                $extension = strtolower(pathinfo($laporan->lampiran_file, PATHINFO_EXTENSION));
                            @endphp
                            @if(in_array($extension, ['jpg', 'jpeg', 'png']))
                                <a href="{{ asset('storage/' . $laporan->lampiran_file) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $laporan->lampiran_file) }}" alt="Lampiran" class="img-fluid rounded border shadow-sm" style="max-height: 250px;">
                                </a>
                            @else
                                <a href="{{ asset('storage/' . $laporan->lampiran_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-pdf-fill me-1 text-danger"></i> Buka Lampiran Dokumen
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- LEMBAR DISPOSISI LINTAS UNIT -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-signpost-split-fill me-2 text-warning"></i>Lembar Disposisi & Sub-Tugas Pendukung
                        <span class="badge bg-secondary rounded-pill ms-1">{{ $laporan->disposisiLaporan->count() }}</span>
                    </h6>
                    @if(Auth::user()->unit_kerja_id == $laporan->unit_tujuan_id || Auth::user()->role == 'admin')
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalKirimDisposisi">
                            <i class="bi bi-plus-lg me-1"></i> Disposisi Baru
                        </button>
                    @endif
                </div>
                <div class="card-body p-4">
                    @if($laporan->disposisiLaporan->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <p class="mb-0 small">Belum ada disposisi ke unit kerja pendukung untuk laporan ini.</p>
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($laporan->disposisiLaporan as $disposisi)
                                <div class="col-12">
                                    <div class="border rounded-3 p-3 bg-white position-relative shadow-sm">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 me-1">
                                                    Dari: {{ $disposisi->unitPengirim->nama_unit ?? '-' }}
                                                </span>
                                                <i class="bi bi-arrow-right text-muted mx-1"></i>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                                    Ke: {{ $disposisi->unitPenerima->nama_unit ?? '-' }}
                                                </span>
                                            </div>
                                            @php
                                                $dispStatusBadge = [
                                                    'Pending' => 'bg-warning text-dark',
                                                    'Diproses' => 'bg-info text-dark',
                                                    'Selesai'  => 'bg-success',
                                                    'Ditolak'  => 'bg-danger',
                                                ][$disposisi->status_disposisi] ?? 'bg-secondary';
                                            @endphp
                                            <span class="badge {{ $dispStatusBadge }}">{{ $disposisi->status_disposisi }}</span>
                                        </div>

                                        <p class="small text-dark mb-2"><strong>Instruksi Disposisi:</strong> {{ $disposisi->catatan_disposisi }}</p>

                                        @if($disposisi->catatan_tindak_lanjut)
                                            <div class="p-2 rounded bg-light border-start border-3 border-success mb-2">
                                                <small class="fw-bold d-block text-success">Tindak Lanjut Unit Penerima:</small>
                                                <small class="text-secondary">{{ $disposisi->catatan_tindak_lanjut }}</small>
                                            </div>
                                        @endif

                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2">
                                            <small class="text-muted" style="font-size: 0.725rem;">
                                                <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($disposisi->tanggal_disposisi)->format('d M Y, H:i') }}
                                                @if($disposisi->petugasDisposisi)
                                                     • Oleh: {{ $disposisi->petugasDisposisi->name }}
                                                @endif
                                            </small>

                                            <div class="d-flex gap-1">
                                                <!-- Tombol Update Disposisi bagi Unit Penerima -->
                                                @if(Auth::user()->unit_kerja_id == $disposisi->unit_penerima_id || Auth::user()->role == 'admin')
                                                    <button type="button" class="btn btn-xs btn-outline-primary btn-update-disposisi" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalUpdateDisposisi"
                                                            data-id="{{ $disposisi->id }}"
                                                            data-status="{{ $disposisi->status_disposisi }}"
                                                            data-catatan="{{ $disposisi->catatan_tindak_lanjut }}">
                                                        <i class="bi bi-pencil me-1"></i> Respond Disposisi
                                                    </button>
                                                @endif

                                                <!-- Batal Disposisi oleh Unit Pengirim -->
                                                @if(Auth::user()->unit_kerja_id == $disposisi->unit_pengirim_id && $disposisi->status_disposisi === 'Pending')
                                                    <form action="{{ route('disposisi.destroy', $disposisi->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan disposisi ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-xs btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Timeline Audit Trail -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Timeline Progress & Audit Trail</h6>
                </div>
                <div class="card-body p-4">
                    <div class="timeline">
                        @foreach($laporan->riwayatLaporan as $riwayat)
                            <div class="border-start border-2 border-primary ps-3 pb-3 position-relative">
                                <div class="position-absolute bg-primary rounded-circle" style="width: 10px; height: 10px; left: -6px; top: 4px;"></div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-secondary" style="font-size: 0.65rem;">{{ $riwayat->status_baru ?? 'Log Event' }}</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ $riwayat->created_at->format('d M, H:i') }}</small>
                                </div>
                                <div class="fw-bold text-dark small">{{ $riwayat->user->name ?? 'Sistem' }}</div>
                                <p class="text-muted small mb-0 mt-1" style="font-size: 0.8rem;">{{ $riwayat->keterangan }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 1: KIRIM DISPOSISI BARU -->
<div class="modal fade" id="modalKirimDisposisi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6 fw-bold"><i class="bi bi-send-plus me-2"></i>Kirim Disposisi Lintas Unit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('disposisi.store', $laporan->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="unit_penerima_id" class="form-label fw-semibold small">Pilih Unit Kerja Pendukung <span class="text-danger">*</span></label>
                        <select class="form-select" id="unit_penerima_id" name="unit_penerima_id" required>
                            <option value="" selected disabled>-- Pilih Unit Kerja Tujuan --</option>
                            @foreach($unitKerjaList as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->nama_unit }} ({{ $unit->kode_unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="catatan_disposisi" class="form-label fw-semibold small">Instruksi / Catatan Disposisi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="catatan_disposisi" name="catatan_disposisi" rows="3" placeholder="Tuliskan instruksi pengerjaan yang diminta ke unit kerja terkait..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i> Kirim Disposisi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 2: RESPOND / UPDATE DISPOSISI -->
<div class="modal fade" id="modalUpdateDisposisi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-dark">
                <h5 class="modal-title fs-6 fw-bold"><i class="bi bi-pencil-square me-2"></i>Tindak Lanjut Disposisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formUpdateDisposisi" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="disp_status" class="form-label fw-semibold small">Status Disposisi Unit <span class="text-danger">*</span></label>
                        <select class="form-select" id="disp_status" name="status_disposisi" required>
                            <option value="Menunggu">Menunggu</option>
                            <option value="Diproses">Diproses</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="disp_catatan" class="form-label fw-semibold small">Catatan Tindak Lanjut Pengerjaan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="disp_catatan" name="catatan_tindak_lanjut" rows="3" placeholder="Jelaskan tindakan yang telah dilakukan oleh unit Anda..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="disp_file" class="form-label fw-semibold small">Unggah Bukti Pengerjaan (Gambar / PDF)</label>
                        <input type="file" class="form-control" id="disp_file" name="lampiran_file" accept="image/*,.pdf">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-dark fw-bold"><i class="bi bi-save me-1"></i> Simpan Response</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 3: UPDATE STATUS UTAMA LAPORAN -->
<div class="modal fade" id="modalUpdateStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fs-6 fw-bold"><i class="bi bi-pencil-square me-2"></i>Update Status Laporan Utama</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('laporan.updateStatus', $laporan->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status_baru" class="form-label fw-semibold small">Pilih Status Baru <span class="text-danger">*</span></label>
                        <select class="form-select" id="status_baru" name="status_baru" required>
                            <option value="Diajukan" {{ $laporan->status_laporan == 'Diajukan' ? 'selected' : '' }}>Diajukan</option>
                            <option value="Diproses" {{ $laporan->status_laporan == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="Selesai" {{ $laporan->status_laporan == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Ditolak" {{ $laporan->status_laporan == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label fw-semibold small">Catatan / Keterangan Penanganan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Tuliskan alasan atau rincian tindakan penanganan laporan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const updateButtons = document.querySelectorAll('.btn-update-disposisi');
        const formUpdate = document.getElementById('formUpdateDisposisi');
        const selectStatus = document.getElementById('disp_status');
        const textareaCatatan = document.getElementById('disp_catatan');

        updateButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const status = this.getAttribute('data-status');
                const catatan = this.getAttribute('data-catatan');

                formUpdate.action = `/disposisi/${id}/status`;
                selectStatus.value = status || 'Diproses';
                textareaCatatan.value = catatan || '';
            });
        });
    });
</script>
@endpush
@endsection
