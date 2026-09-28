<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Filter berdasarkan jenis tampilan (Laporan Masuk Unit vs Laporan Saya)
        if ($request->get('view_type') === 'incoming') {
            $userUnitId = Auth::user()->unit_kerja_id;
            $query->where('unit_tujuan_id', $userUnitId);
        } else {
            // Jika memilih view laporan saya / default
            if (in_array(Auth::user()->role, ['petugas', 'kepala_unit'])) {
                $query->where(function ($q) {
                    $q->where('user_id', Auth::id())
                      ->orWhere('unit_asal_id', Auth::user()->unit_kerja_id);
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
        $unitKerjaList = UnitKerja::all();
        $masterLaporanList = MasterLaporan::where('status_aktif', true)->get();

        return view('laporan.index', compact('laporanList', 'unitKerjaList', 'masterLaporanList'));
    }

    /**
     * Menampilkan form pembuatan laporan baru.
     */
    public function create()
    {
        $masterLaporanList = MasterLaporan::where('status_aktif', true)->get();
        $unitKerjaList = UnitKerja::where('id', '!=', Auth::user()->unit_kerja_id)->get();

        return view('laporan.create', compact('masterLaporanList', 'unitKerjaList'));
    }

    /**
     * Menyimpan data laporan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'master_laporan_id'  => ['required', 'exists:master_laporan,id'],
            'unit_tujuan_id'     => ['required', 'exists:unit_kerja,id'],
            'judul_laporan'      => ['required', 'string', 'max:255'],
            'deskripsi_kejadian' => ['required', 'string'],
            'prioritas'          => ['required', 'in:Rendah,Sedang,Tinggi,Darurat'],
            'tindakan_awal'      => ['nullable', 'string'],
            'foto_lampiran'      => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'master_laporan_id.required'  => 'Kategori laporan wajib dipilih.',
            'unit_tujuan_id.required'     => 'Unit tujuan laporan wajib dipilih.',
            'judul_laporan.required'      => 'Judul laporan wajib diisi.',
            'deskripsi_kejadian.required' => 'Rincian deskripsi laporan wajib diisi.',
            'prioritas.required'          => 'Tingkat prioritas wajib dipilih.',
            'foto_lampiran.image'         => 'Berkas lampiran harus berupa gambar (JPG/PNG).',
            'foto_lampiran.max'           => 'Ukuran foto lampiran maksimal 2MB.',
        ]);

        $masterLaporan = MasterLaporan::findOrFail($request->master_laporan_id);

        // Generate Kode Transaksi Otomatis (Contoh: RUT-20260928-001 / INC-20260928-001)
        $prefix = ($masterLaporan->jenis_laporan === 'Rutin') ? 'RUT' : 'INC';
        $todayDate = now()->format('Ymd');
        $randomSequence = strtoupper(Str::random(3));
        $kodeTransaksi = "{$prefix}-{$todayDate}-{$randomSequence}";

        // Upload Foto Lampiran jika ada
        $fotoPath = null;
        if ($request->hasFile('foto_lampiran')) {
            $fotoPath = $request->file('foto_lampiran')->store('lampiran_laporan', 'public');
        }

        // Simpan Detail Laporan
        $laporan = DetailLaporan::create([
            'kode_transaksi'     => $kodeTransaksi,
            'user_id'            => Auth::id(),
            'unit_asal_id'       => Auth::user()->unit_kerja_id,
            'unit_tujuan_id'     => $request->unit_tujuan_id,
            'master_laporan_id'  => $request->master_laporan_id,
            'tanggal_kejadian'   => now(),
            'judul_laporan'      => $request->judul_laporan,
            'deskripsi_kejadian' => $request->deskripsi_kejadian,
            'tindakan_awal'      => $request->tindakan_awal,
            'prioritas'          => $request->prioritas,
            'status_laporan'     => 'Diajukan',
            'foto_lampiran'      => $fotoPath,
        ]);

        // Pencatatan Audit Trail / History Awal
        RiwayatLaporan::create([
            'detail_laporan_id' => $laporan->id,
            'user_id'           => Auth::id(),
            'status_sebelumnya' => null,
            'status_baru'       => 'Diajukan',
            'keterangan'        => 'Laporan baru berhasil diajukan ke unit tujuan.',
        ]);

        return redirect()->route('laporan.index')
            ->with('success', "Laporan [{$kodeTransaksi}] berhasil dibuat dan dikirim.");
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
            'riwayatLaporan.user'
        ])->findOrFail($id);

        return view('laporan.show', compact('laporan'));
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
        $statusSebelumnya = $laporan->status_laporan;

        // Update data laporan
        $laporan->status_laporan = $request->status_baru;

        // Jika status diubah menjadi Diproses dan belum ada petugas, tetapkan user login sebagai petugas
        if ($request->status_baru === 'Diproses' && !$laporan->petugas_penangan_id) {
            $laporan->petugas_penangan_id = Auth::id();
        }

        // Jika status Selesai, catat tanggal penyelesaian
        if ($request->status_baru === 'Selesai') {
            $laporan->tanggal_penyelesaian = now();
        }

        $laporan->save();

        // Tambahkan Log ke Riwayat Laporan
        RiwayatLaporan::create([
            'detail_laporan_id' => $laporan->id,
            'user_id'           => Auth::id(),
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru'       => $request->status_baru,
            'keterangan'        => $request->keterangan,
        ]);

        return redirect()->back()
            ->with('success', "Status laporan {$laporan->kode_transaksi} berhasil diperbarui menjadi '{$request->status_baru}'.");
    }
}
