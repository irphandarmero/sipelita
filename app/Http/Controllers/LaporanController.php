<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\DetailLaporan;
use App\Models\MasterLaporan;
use App\Models\UnitKerja;
use App\Models\RiwayatLaporan;

class LaporanController extends Controller
{
    /**
     * Menampilkan daftar laporan dengan filter dan pencarian.
     */
    public function index(Request $request)
    {
        $query = DetailLaporan::with(['user', 'unitAsal', 'unitTujuan', 'masterLaporan', 'petugasPenangan'])
            ->latest();

        $availableToDestination = function ($scope) {
            $scope->whereNull('status_peninjauan')
                ->orWhere('status_peninjauan', 'Disetujui');
        };

        // Filter berdasarkan jenis tampilan (Laporan Masuk Unit vs Laporan Saya)
        if ($request->get('view_type') === 'incoming') {
            $userUnitId = Auth::user()->unit_kerja_id;
            $query->where(function ($scope) use ($userUnitId) {
                $scope->where(function ($destination) use ($userUnitId) {
                    $destination->where('unit_tujuan_id', $userUnitId)
                        ->where(function ($review) {
                            $review->whereNull('status_peninjauan')
                                ->orWhere('status_peninjauan', 'Disetujui');
                        });
                })
                    ->orWhereHas('disposisiLaporan', function ($disposisiQuery) use ($userUnitId) {
                        $disposisiQuery->where('unit_penerima_id', $userUnitId);
                        $disposisiQuery->whereHas('detailLaporan', function ($laporanQuery) {
                            $laporanQuery->whereNull('status_peninjauan')
                                ->orWhere('status_peninjauan', 'Disetujui');
                        });
                    });
            });
        } else {
            // Jika memilih view laporan saya / default
            if (!in_array(Auth::user()->role, ['admin', 'direksi'])) {
                $userUnitId = Auth::user()->unit_kerja_id;
                $query->where(function ($q) use ($availableToDestination, $userUnitId) {
                    $q->where('user_id', Auth::id())
                      ->orWhere('unit_asal_id', $userUnitId)
                      ->orWhere(function ($destination) use ($availableToDestination, $userUnitId) {
                          $destination->where('unit_tujuan_id', $userUnitId)
                              ->where($availableToDestination);
                      })
                      ->orWhereHas('disposisiLaporan', function ($disposisiQuery) use ($userUnitId) {
                          $disposisiQuery->where(function ($units) use ($userUnitId) {
                              $units->where('unit_pengirim_id', $userUnitId)
                                  ->orWhere('unit_penerima_id', $userUnitId);
                          })->whereHas('detailLaporan', function ($laporanQuery) {
                              $laporanQuery->whereNull('status_peninjauan')
                                  ->orWhere('status_peninjauan', 'Disetujui');
                          });
                      });
                });
            }
        }

        // Filter Pencarian Text (Kode Transaksi, Judul, Pelapor)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'LIKE', "%{$search}%")
                  ->orWhere('judul_laporan', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter Jenis Laporan (Rutin / Insidentil)
        if ($request->filled('jenis')) {
            $jenis = $request->jenis;
            $query->whereHas('masterLaporan', function ($m) use ($jenis) {
                $m->where('jenis_laporan', $jenis);
            });
        }

        // Filter Status Laporan
        if ($request->filled('status')) {
            $query->where('status_laporan', $request->status);
        }

        // Filter Prioritas
        if ($request->filled('prioritas')) {
            $query->where('prioritas', $request->prioritas);
        }

        // Filter Unit Asal & Tujuan
        if ($request->filled('unit_asal_id')) {
            $query->where('unit_asal_id', $request->unit_asal_id);
        }
        if ($request->filled('unit_tujuan_id')) {
            $query->where('unit_tujuan_id', $request->unit_tujuan_id);
        }

        $laporanList = $query->paginate(10)->withQueryString();
        $laporanList->getCollection()->transform(function (DetailLaporan $laporan) {
            $laporan->setAttribute('can_edit', $this->canEditReport($laporan));

            return $laporan;
        });
        $unitKerjaList = UnitKerja::all();
        $masterLaporanList = MasterLaporan::where('status_aktif', true)->get();

        return view('laporan.index', compact('laporanList', 'unitKerjaList', 'masterLaporanList'));
    }

    /**
     * Menampilkan form pembuatan laporan baru.
     */
    public function create()
    {
        abort_unless(Auth::user()->unit_kerja_id, 403, 'Akun Anda belum terhubung ke unit kerja.');

        $masterLaporanList = MasterLaporan::where('status_aktif', true)->get();
        $unitKerjaList = UnitKerja::where('id', '!=', Auth::user()->unit_kerja_id)->get();

        $isEditing = false;

        return view('laporan.create', compact('masterLaporanList', 'unitKerjaList', 'isEditing'));
    }

    /**
     * Menampilkan formulir edit laporan kepada pembuat sebelum laporan diproses.
     */
    public function edit($id)
    {
        $laporan = DetailLaporan::with('masterLaporan')->findOrFail($id);
        abort_unless($this->canEditReport($laporan), 403, 'Laporan ini tidak dapat diedit oleh akun Anda.');

        $masterLaporanList = MasterLaporan::where('status_aktif', true)->get();
        $unitKerjaList = UnitKerja::where('id', '!=', $laporan->unit_asal_id)->get();
        $isEditing = true;

        return view('laporan.create', compact('laporan', 'masterLaporanList', 'unitKerjaList', 'isEditing'));
    }

    /**
     * Menyimpan perubahan laporan sebelum keputusan tinjauan atau penanganan.
     */
    public function update(Request $request, $id)
    {
        $laporan = DetailLaporan::with('masterLaporan')->findOrFail($id);
        abort_unless($this->canEditReport($laporan), 403, 'Laporan ini tidak dapat diedit oleh akun Anda.');

        $validated = $request->validate([
            'master_laporan_id'  => ['required', Rule::exists('master_laporan', 'id')->where('status_aktif', true)],
            'unit_tujuan_id'     => ['required', 'exists:unit_kerja,id', Rule::notIn([$laporan->unit_asal_id])],
            'tanggal_kejadian'   => ['required', 'date_format:Y-m-d\TH:i'],
            'judul_laporan'      => ['required', 'string', 'max:200'],
            'deskripsi_kejadian' => ['required', 'string'],
            'prioritas'          => ['required', 'in:Rendah,Sedang,Tinggi,Darurat'],
            'tindakan_awal'      => [
                Rule::requiredIf(function () use ($request) {
                    return MasterLaporan::whereKey($request->input('master_laporan_id'))
                        ->value('jenis_laporan') === 'Insidentil';
                }),
                'nullable',
                'string',
                'max:2000',
            ],
            'lampiran_file'      => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'unit_tujuan_id.not_in'       => 'Unit tujuan harus berbeda dengan unit asal.',
            'tindakan_awal.required'      => 'Tindakan awal wajib dijelaskan untuk laporan Insidentil.',
            'lampiran_file.mimes'         => 'Berkas lampiran harus berupa JPG, JPEG, PNG, atau PDF.',
            'lampiran_file.max'           => 'Ukuran berkas lampiran maksimal 5MB.',
        ]);

        $masterLaporan = MasterLaporan::where('status_aktif', true)->findOrFail($validated['master_laporan_id']);
        $lampiranPath = $laporan->lampiran_file;
        if ($request->hasFile('lampiran_file')) {
            $lampiranPath = $request->file('lampiran_file')->store('lampiran_laporan', 'public');
            if ($lampiranPath === false) {
                throw new \RuntimeException('Gagal menyimpan lampiran laporan.');
            }
        }

        $updated = DB::transaction(function () use ($id, $validated, $masterLaporan, $lampiranPath) {
            $laporan = DetailLaporan::whereKey($id)->lockForUpdate()->firstOrFail();
            if (!$this->canEditReport($laporan)) {
                return false;
            }

            $status = $laporan->status_laporan;
            $laporan->update([
                'master_laporan_id'  => $validated['master_laporan_id'],
                'unit_tujuan_id'     => $validated['unit_tujuan_id'],
                'tanggal_kejadian'   => $validated['tanggal_kejadian'],
                'judul_laporan'      => $validated['judul_laporan'],
                'deskripsi_kejadian' => $validated['deskripsi_kejadian'],
                'tindakan_awal'      => $masterLaporan->jenis_laporan === 'Insidentil'
                    ? ($validated['tindakan_awal'] ?? null)
                    : null,
                'prioritas'          => $validated['prioritas'],
                'lampiran_file'      => $lampiranPath,
            ]);

            RiwayatLaporan::create([
                'detail_laporan_id' => $laporan->id,
                'user_id'           => Auth::id(),
                'status_sebelumnya' => $status,
                'status_baru'       => $status,
                'keterangan'        => 'Isi laporan diperbarui.',
            ]);

            return true;
        });

        if (!$updated) {
            return redirect()->back()->with('error', 'Laporan sudah tidak dapat diedit karena status atau keputusan tinjauannya telah berubah.');
        }

        return redirect()->route('laporan.show', $id)->with('success', 'Perubahan laporan berhasil disimpan.');
    }

    /**
     * Menyimpan data laporan baru ke database.
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->unit_kerja_id, 403, 'Akun Anda belum terhubung ke unit kerja.');

        $validated = $request->validate([
            'master_laporan_id'  => ['required', Rule::exists('master_laporan', 'id')->where('status_aktif', true)],
        ]);

        $masterLaporan = MasterLaporan::where('status_aktif', true)->findOrFail($validated['master_laporan_id']);

        $validated = array_merge($validated, $request->validate([
            'unit_tujuan_id'     => ['required', 'exists:unit_kerja,id', Rule::notIn([Auth::user()->unit_kerja_id])],
            'tanggal_kejadian'   => ['required', 'date_format:Y-m-d\TH:i'],
            'judul_laporan'      => ['required', 'string', 'max:200'],
            'deskripsi_kejadian' => ['required', 'string'],
            'prioritas'          => ['required', 'in:Rendah,Sedang,Tinggi,Darurat'],
            'tindakan_awal'      => [
                Rule::requiredIf($masterLaporan->jenis_laporan === 'Insidentil'),
                'nullable',
                'string',
                'max:2000',
            ],
            'lampiran_file'      => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'master_laporan_id.required'  => 'Kategori laporan wajib dipilih.',
            'unit_tujuan_id.required'     => 'Unit tujuan laporan wajib dipilih.',
            'unit_tujuan_id.not_in'       => 'Unit tujuan harus berbeda dengan unit asal.',
            'tanggal_kejadian.required'   => 'Tanggal dan waktu kejadian wajib diisi.',
            'judul_laporan.required'      => 'Judul laporan wajib diisi.',
            'deskripsi_kejadian.required' => 'Rincian deskripsi laporan wajib diisi.',
            'prioritas.required'          => 'Tingkat prioritas wajib dipilih.',
            'tindakan_awal.required'      => 'Tindakan awal wajib dijelaskan untuk laporan Insidentil.',
            'lampiran_file.mimes'         => 'Berkas lampiran harus berupa JPG, JPEG, PNG, atau PDF.',
            'lampiran_file.max'           => 'Ukuran berkas lampiran maksimal 5MB.',
        ]));

        // Generate Kode Transaksi Otomatis (Contoh: RUT-20260928-001 / INC-20260928-001)
        $prefix = ($masterLaporan->jenis_laporan === 'Rutin') ? 'RUT' : 'INC';
        $todayDate = now()->format('Ymd');
        $randomSequence = strtoupper(Str::random(3));
        $kodeTransaksi = "{$prefix}-{$todayDate}-{$randomSequence}";

        // Upload Foto Lampiran jika ada
        $lampiranPath = null;
        if ($request->hasFile('lampiran_file')) {
            $lampiranPath = $request->file('lampiran_file')->store('lampiran_laporan', 'public');
            if ($lampiranPath === false) {
                throw new \RuntimeException('Gagal menyimpan lampiran laporan.');
            }
        }

        $needsReview = Auth::user()->role === 'petugas';
        DB::transaction(function () use ($validated, $kodeTransaksi, $lampiranPath, $masterLaporan, $needsReview) {
            $laporan = DetailLaporan::create([
                'kode_transaksi'     => $kodeTransaksi,
                'user_id'            => Auth::id(),
                'unit_asal_id'       => Auth::user()->unit_kerja_id,
                'unit_tujuan_id'     => $validated['unit_tujuan_id'],
                'master_laporan_id'  => $validated['master_laporan_id'],
                'tanggal_kejadian'   => $validated['tanggal_kejadian'],
                'judul_laporan'      => $validated['judul_laporan'],
                'deskripsi_kejadian' => $validated['deskripsi_kejadian'],
                'tindakan_awal'      => $masterLaporan->jenis_laporan === 'Insidentil'
                    ? ($validated['tindakan_awal'] ?? null)
                    : null,
                'prioritas'          => $validated['prioritas'],
                'status_laporan'     => 'Diajukan',
                'status_peninjauan'  => $needsReview ? 'Menunggu' : null,
                'lampiran_file'      => $lampiranPath,
            ]);

            RiwayatLaporan::create([
                'detail_laporan_id' => $laporan->id,
                'user_id'           => Auth::id(),
                'status_sebelumnya' => null,
                'status_baru'       => 'Diajukan',
                'keterangan'        => $needsReview
                    ? 'Laporan diajukan dan menunggu peninjauan Kepala Unit.'
                    : 'Laporan baru berhasil diajukan ke unit tujuan.',
            ]);
        });

        return redirect()->route('laporan.index')
            ->with('success', $needsReview
                ? "Laporan [{$kodeTransaksi}] berhasil dibuat dan menunggu peninjauan Kepala Unit."
                : "Laporan [{$kodeTransaksi}] berhasil dibuat dan dikirim.");
    }

    /**
     * Menampilkan detail laporan beserta histori audit trail-nya.
     */
    public function show($id)
    {
        $laporan = DetailLaporan::with([
            'user',
            'unitAsal',
            'unitTujuan',
            'masterLaporan',
            'petugasPenangan',
            'riwayatLaporan.user',
            'disposisiLaporan.unitPengirim',
            'disposisiLaporan.unitPenerima',
            'disposisiLaporan.userPengirim',
            'disposisiLaporan.petugasTindakLanjut',
        ])->findOrFail($id);

        $user = Auth::user();
        $reviewPendingOrRejected = in_array($laporan->status_peninjauan, ['Menunggu', 'Ditolak'], true);
        $canViewAsDestination = !$reviewPendingOrRejected
            && $user->unit_kerja_id == $laporan->unit_tujuan_id;
        $canView = in_array($user->role, ['admin', 'direksi'])
            || $user->id === $laporan->user_id
            || $user->id === $laporan->petugas_penangan_id
            || $user->unit_kerja_id == $laporan->unit_asal_id
            || $canViewAsDestination
            || (!$reviewPendingOrRejected && $laporan->disposisiLaporan()->where(function ($query) use ($user) {
                $query->where('unit_pengirim_id', $user->unit_kerja_id)
                    ->orWhere('unit_penerima_id', $user->unit_kerja_id);
            })->exists());
        abort_unless($canView, 403);

        $unitKerjaList = UnitKerja::where('id', '!=', $laporan->unit_tujuan_id)->get();
        $canManageStatus = $laporan->status_laporan !== 'Selesai'
            && !in_array($laporan->status_peninjauan, ['Menunggu', 'Ditolak'], true)
            && ($user->role === 'admin' || $user->unit_kerja_id == $laporan->unit_tujuan_id);
        $canReview = $laporan->status_peninjauan === 'Menunggu'
            && ($user->role === 'admin'
                || ($user->role === 'kepala_unit' && $user->unit_kerja_id == $laporan->unit_asal_id));
        $isIncidentil = $laporan->masterLaporan && $laporan->masterLaporan->jenis_laporan === 'Insidentil';
        $canCreateDisposisi = $isIncidentil
            && !$reviewPendingOrRejected
            && !in_array($laporan->status_laporan, ['Selesai', 'Ditolak'], true)
            && ($canManageStatus || $user->id == $laporan->petugas_penangan_id);
        $canEdit = $this->canEditReport($laporan);

        return view('laporan.show', compact('laporan', 'unitKerjaList', 'canManageStatus', 'canCreateDisposisi', 'canReview', 'isIncidentil', 'canEdit'));
    }

    /**
     * Menyetujui atau menolak laporan petugas melalui Kepala Unit asal.
     */
    public function review(Request $request, $id)
    {
        $validated = $request->validate([
            'keputusan' => ['required', 'in:Disetujui,Ditolak'],
            'keterangan' => [
                Rule::requiredIf($request->input('keputusan') === 'Ditolak'),
                'nullable',
                'string',
                'max:500',
            ],
        ], [
            'keputusan.required' => 'Keputusan peninjauan wajib dipilih.',
            'keterangan.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $user = Auth::user();
        abort_unless(in_array($user->role, ['admin', 'kepala_unit'], true), 403);

        $reviewed = DB::transaction(function () use ($id, $validated, $user) {
            $laporan = DetailLaporan::whereKey($id)->lockForUpdate()->firstOrFail();
            abort_unless(
                $user->role === 'admin' || $user->unit_kerja_id == $laporan->unit_asal_id,
                403
            );

            if ($laporan->status_peninjauan !== 'Menunggu') {
                return false;
            }

            $statusSebelumnya = $laporan->status_laporan;
            $laporan->status_peninjauan = $validated['keputusan'];
            if ($validated['keputusan'] === 'Ditolak') {
                $laporan->status_laporan = 'Ditolak';
            }
            $laporan->save();

            $keterangan = $validated['keputusan'] === 'Ditolak'
                ? "Kepala Unit menolak laporan. Alasan: {$validated['keterangan']}"
                : 'Kepala Unit menyetujui laporan untuk diteruskan ke unit tujuan.';
            RiwayatLaporan::create([
                'detail_laporan_id' => $laporan->id,
                'user_id' => $user->id,
                'status_sebelumnya' => $statusSebelumnya,
                'status_baru' => $laporan->status_laporan,
                'keterangan' => $keterangan,
            ]);

            return true;
        });

        if (!$reviewed) {
            return redirect()->back()->with('error', 'Laporan ini sudah ditinjau atau tidak lagi menunggu keputusan.');
        }

        return redirect()->back()->with(
            'success',
            $validated['keputusan'] === 'Ditolak'
                ? 'Laporan ditolak dan tidak diteruskan ke unit tujuan.'
                : 'Laporan disetujui dan diteruskan ke unit tujuan.'
        );
    }

    /**
     * Memperbarui status laporan dan mencatat histori perjalanan status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_baru' => ['required', 'in:Diajukan,Diproses,Selesai,Ditolak'],
            'keterangan'  => ['required', 'string', 'max:500'],
        ], [
            'status_baru.required' => 'Pilihan status baru wajib ditentukan.',
            'keterangan.required'  => 'Catatan tindak lanjut/keterangan wajib diisi.',
        ]);

        $laporan = DetailLaporan::findOrFail($id);
        if ($laporan->status_laporan === 'Selesai') {
            return redirect()->back()->with('error', 'Laporan yang sudah selesai tidak dapat diubah lagi.');
        }
        if (in_array($laporan->status_peninjauan, ['Menunggu', 'Ditolak'], true)) {
            return redirect()->back()->with('error', 'Laporan harus disetujui Kepala Unit sebelum dapat diproses.');
        }

        // 1. Otorisasi Hak Akses Unit
        if (Auth::user()->role !== 'admin' && Auth::user()->unit_kerja_id != $laporan->unit_tujuan_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menindaklanjuti atau mengubah status laporan unit ini.');
        }

        // 2. Validasi Status Laporan jika sudah Selesai / Ditolak
        if (in_array($laporan->status_laporan, ['Selesai', 'Ditolak']) && Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', "Laporan berstatus '{$laporan->status_laporan}' tidak dapat diubah statusnya.");
        }

        return DB::transaction(function () use ($id, $request) {
            $laporan = DetailLaporan::whereKey($id)->lockForUpdate()->firstOrFail();
            if ($laporan->status_laporan === 'Selesai') {
                return redirect()->back()->with('error', 'Laporan yang sudah selesai tidak dapat diubah lagi.');
            }
            if (in_array($laporan->status_peninjauan, ['Menunggu', 'Ditolak'], true)) {
                return redirect()->back()->with('error', 'Laporan harus disetujui Kepala Unit sebelum dapat diproses.');
            }

            if (Auth::user()->role !== 'admin' && Auth::user()->unit_kerja_id != $laporan->unit_tujuan_id) {
                abort(403);
            }

            if (in_array($laporan->status_laporan, ['Selesai', 'Ditolak']) && Auth::user()->role !== 'admin') {
                return redirect()->back()->with('error', "Laporan berstatus '{$laporan->status_laporan}' tidak dapat diubah statusnya.");
            }

            if ($request->status_baru === 'Selesai') {
                $pendingDisposisiCount = $laporan->disposisiLaporan()
                    ->whereIn('status_disposisi', ['Pending', 'Diproses'])
                    ->count();

                if ($pendingDisposisiCount > 0) {
                    return redirect()->back()->with('error', "Laporan tidak dapat diselesaikan karena masih terdapat {$pendingDisposisiCount} tugas disposisi ke unit lain yang berstatus Menunggu atau Diproses.");
                }

            }

            $statusSebelumnya = $laporan->status_laporan;
            $laporan->status_laporan = $request->status_baru;

            if ($request->status_baru === 'Diproses' && !$laporan->petugas_penangan_id) {
                $laporan->petugas_penangan_id = Auth::id();
            }

            $laporan->tanggal_penyelesaian = $request->status_baru === 'Selesai' ? now() : null;
            $laporan->save();

            RiwayatLaporan::create([
                'detail_laporan_id' => $laporan->id,
                'user_id'           => Auth::id(),
                'status_sebelumnya' => $statusSebelumnya,
                'status_baru'       => $request->status_baru,
                'keterangan'        => $request->keterangan,
            ]);

            return redirect()->back()
                ->with('success', "Status laporan {$laporan->kode_transaksi} berhasil diperbarui menjadi '{$request->status_baru}'.");
        });
    }

    private function canEditReport(DetailLaporan $laporan): bool
    {
        return (Auth::user()->role === 'admin' || (int) $laporan->user_id === (int) Auth::id())
            && $laporan->status_laporan === 'Diajukan'
            && in_array($laporan->status_peninjauan, [null, 'Menunggu'], true);
    }
}
