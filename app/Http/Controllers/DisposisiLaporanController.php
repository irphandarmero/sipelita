<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DetailLaporan;
use App\Models\DisposisiLaporan;
use App\Models\RiwayatLaporan;
use App\Models\UnitKerja;
use Illuminate\Support\Facades\DB;

class DisposisiLaporanController extends Controller
{
    /**
     * Membuat & Mengirimkan lembar disposisi laporan ke unit kerja pendukung.
     */
    public function store(Request $request, $laporanId)
    {
        $request->validate([
            'unit_penerima_id'  => ['required', 'exists:unit_kerja,id'],
            'catatan_disposisi' => ['required', 'string', 'max:1000'],
        ], [
            'unit_penerima_id.required'  => 'Unit penerima disposisi wajib dipilih.',
            'catatan_disposisi.required' => 'Catatan instruksi disposisi wajib diisi.',
        ]);

        $laporan = DetailLaporan::with('masterLaporan')->findOrFail($laporanId);
        if (!$laporan->masterLaporan || $laporan->masterLaporan->jenis_laporan !== 'Insidentil') {
            return redirect()->back()->with('error', 'Disposisi hanya tersedia untuk laporan Insidentil.');
        }
        if (in_array($laporan->status_peninjauan, ['Menunggu', 'Ditolak'], true)) {
            return redirect()->back()->with('error', 'Laporan harus disetujui Kepala Unit sebelum dapat didisposisikan.');
        }
        $user = Auth::user();

        if ($user->role !== 'admin' && $user->unit_kerja_id != $laporan->unit_tujuan_id && $user->id != $laporan->petugas_penangan_id) {
            return redirect()->back()->with('error', 'Hanya Unit Penanggung Jawab Laporan atau Admin yang berhak memberikan disposisi.');
        }

        // 2. Validasi Status Laporan Utama: Tidak bisa disposisi jika laporan sudah Selesai / Ditolak
        if (in_array($laporan->status_laporan, ['Selesai', 'Ditolak'])) {
            return redirect()->back()->with('error', "Disposisi tidak dapat dikirimkan pada laporan berstatus '{$laporan->status_laporan}'.");
        }

        // 3. Mencegah disposisi ke unit kerja sendiri
        if ($request->unit_penerima_id == $laporan->unit_tujuan_id) {
            return redirect()->back()->with('error', 'Disposisi tidak dapat dikirimkan ke unit kerja Anda sendiri.');
        }

        // 4. Mencegah disposisi ganda yang masih berjalan ke unit yang sama
        $existingPending = $laporan->disposisiLaporan()
            ->where('unit_penerima_id', $request->unit_penerima_id)
            ->whereIn('status_disposisi', ['Pending', 'Diproses'])
            ->exists();

        if ($existingPending) {
            return redirect()->back()->with('error', 'Unit tersebut masih memiliki tugas disposisi aktif yang belum diselesaikan pada laporan ini.');
        }

        $unitPenerima = UnitKerja::findOrFail($request->unit_penerima_id);

        DB::transaction(function () use ($laporan, $request, $unitPenerima) {
            $laporan = DetailLaporan::whereKey($laporan->id)->lockForUpdate()->firstOrFail();
            if (in_array($laporan->status_peninjauan, ['Menunggu', 'Ditolak'], true)) {
                abort(409, 'Laporan harus disetujui Kepala Unit sebelum dapat didisposisikan.');
            }
            if (in_array($laporan->status_laporan, ['Selesai', 'Ditolak'], true)) {
                abort(409, "Disposisi tidak dapat dikirimkan pada laporan berstatus '{$laporan->status_laporan}'.");
            }
            if ($laporan->disposisiLaporan()
                ->where('unit_penerima_id', $request->unit_penerima_id)
                ->whereIn('status_disposisi', ['Pending', 'Diproses'])
                ->exists()) {
                abort(409, 'Unit tersebut masih memiliki tugas disposisi aktif yang belum diselesaikan pada laporan ini.');
            }

            $statusSebelumnya = $laporan->status_laporan;
            DisposisiLaporan::create([
                'detail_laporan_id' => $laporan->id,
                'unit_pengirim_id'  => $laporan->unit_tujuan_id,
                'petugas_disposisi_id' => Auth::id(),
                'unit_penerima_id'  => $request->unit_penerima_id,
                'catatan_disposisi' => $request->catatan_disposisi,
                'status_disposisi'  => 'Pending',
                'tanggal_disposisi' => now(),
            ]);

            if ($laporan->status_laporan === 'Diajukan') {
                $laporan->status_laporan = 'Diproses';
                $laporan->petugas_penangan_id = $laporan->petugas_penangan_id ?: Auth::id();
                $laporan->save();
            }

            RiwayatLaporan::create([
                'detail_laporan_id' => $laporan->id,
                'user_id'           => Auth::id(),
                'status_sebelumnya' => $statusSebelumnya,
                'status_baru'       => $laporan->status_laporan,
                'keterangan'        => "Disposisi dikirim ke unit [{$unitPenerima->nama_unit}]: {$request->catatan_disposisi}",
            ]);
        });

        return redirect()->back()
            ->with('success', "Disposisi laporan berhasil dikirimkan ke unit {$unitPenerima->nama_unit}.");
    }

    /**
     * Memperbarui status dan tindak lanjut lembar disposisi oleh unit penerima.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_disposisi'      => ['required', 'in:Pending,Diproses,Selesai,Ditolak'],
            'catatan_tindak_lanjut' => ['required', 'string', 'max:1000'],
            'lampiran_disposisi'    => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'status_disposisi.required'      => 'Status disposisi wajib ditentukan.',
            'catatan_tindak_lanjut.required' => 'Catatan tindak lanjut wajib diisi.',
            'lampiran_disposisi.max'          => 'Ukuran berkas lampiran maksimal 5MB.',
        ]);

        $disposisi = DisposisiLaporan::with(['detailLaporan.masterLaporan', 'unitPenerima'])->findOrFail($id);
        if (!$disposisi->detailLaporan->masterLaporan
            || $disposisi->detailLaporan->masterLaporan->jenis_laporan !== 'Insidentil') {
            return redirect()->back()->with('error', 'Disposisi hanya tersedia untuk laporan Insidentil.');
        }
        if (in_array($disposisi->detailLaporan->status_peninjauan, ['Menunggu', 'Ditolak'], true)) {
            return redirect()->back()->with('error', 'Laporan belum disetujui Kepala Unit untuk diproses.');
        }
        if (Auth::user()->role !== 'admin' && Auth::user()->unit_kerja_id != $disposisi->unit_penerima_id) {
            abort(403);
        }

        $lampiranPath = null;
        if ($request->hasFile('lampiran_disposisi')) {
            $lampiranPath = $request->file('lampiran_disposisi')->store('lampiran_disposisi', 'public');
            if ($lampiranPath === false) {
                throw new \RuntimeException('Gagal menyimpan lampiran tindak lanjut disposisi.');
            }
        }

        DB::transaction(function () use ($disposisi, $request, $lampiranPath) {
            $statusLaporan = $disposisi->detailLaporan->status_laporan;
            $disposisi->status_disposisi = $request->status_disposisi;
            $disposisi->catatan_tindak_lanjut = $request->catatan_tindak_lanjut;
            $disposisi->petugas_tindak_lanjut_id = Auth::id();

            if ($lampiranPath) {
                $disposisi->lampiran_disposisi = $lampiranPath;
            }

            if ($request->status_disposisi !== 'Pending' && !$disposisi->tanggal_diterima) {
                $disposisi->tanggal_diterima = now();
            }
            $disposisi->tanggal_selesai = $request->status_disposisi === 'Selesai' ? now() : null;
            $disposisi->save();

            RiwayatLaporan::create([
                'detail_laporan_id' => $disposisi->detail_laporan_id,
                'user_id'           => Auth::id(),
                'status_sebelumnya' => $statusLaporan,
                'status_baru'       => $statusLaporan,
                'keterangan'        => "Disposisi [Unit {$disposisi->unitPenerima->nama_unit}] diperbarui menjadi '{$request->status_disposisi}': {$request->catatan_tindak_lanjut}",
            ]);
        });

        return redirect()->back()
            ->with('success', "Status disposisi unit {$disposisi->unitPenerima->nama_unit} berhasil diperbarui.");
    }

    /**
     * Menghapus lembar disposisi (jika dibatalkan oleh unit pengirim).
     */
    public function destroy($id)
    {
        $disposisi = DisposisiLaporan::with(['detailLaporan.masterLaporan', 'unitPenerima'])->findOrFail($id);
        if (!$disposisi->detailLaporan->masterLaporan
            || $disposisi->detailLaporan->masterLaporan->jenis_laporan !== 'Insidentil') {
            return redirect()->back()->with('error', 'Disposisi hanya tersedia untuk laporan Insidentil.');
        }
        if (Auth::user()->role !== 'admin' && Auth::user()->unit_kerja_id != $disposisi->unit_pengirim_id) {
            abort(403);
        }

        $laporanId = $disposisi->detail_laporan_id;
        $unitNama  = $disposisi->unitPenerima->nama_unit ?? 'Tujuan';

        // Hanya bisa dihapus jika statusnya masih Menunggu
        if ($disposisi->status_disposisi !== 'Pending') {
            return redirect()->back()->with('error', 'Disposisi yang sudah diproses/selesai tidak dapat dibatalkan.');
        }

        DB::transaction(function () use ($disposisi, $laporanId, $unitNama) {
            $status = $disposisi->detailLaporan->status_laporan;
            $disposisi->delete();

            RiwayatLaporan::create([
                'detail_laporan_id' => $laporanId,
                'user_id'           => Auth::id(),
                'status_sebelumnya' => $status,
                'status_baru'       => $status,
                'keterangan'        => "Disposisi ke unit [{$unitNama}] telah dibatalkan.",
            ]);
        });

        return redirect()->back()->with('success', 'Disposisi berhasil dibatalkan/dihapus.');
    }
}
