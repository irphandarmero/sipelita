<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DetailLaporan;
use App\Models\DisposisiLaporan;
use App\Models\RiwayatLaporan;
use App\Models\UnitKerja;

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

        $laporan = DetailLaporan::findOrFail($laporanId);

       // 1. Otorisasi Hak Akses: Hanya Unit Penanggung Jawab / Tujuan atau Admin yang dapat membuat disposisi
        if (Auth::user()->role !== 'admin' && Auth::user()->unit_kerja_id != $laporan->unit_tujuan_id) {
            return redirect()->back()->with('error', 'Hanya Unit Penanggung Jawab Laporan atau Admin yang berhak memberikan disposisi.');
        }

        // 2. Validasi Status Laporan Utama: Tidak bisa disposisi jika laporan sudah Selesai / Ditolak
        if (in_array($laporan->status_laporan, ['Selesai', 'Ditolak'])) {
            return redirect()->back()->with('error', "Disposisi tidak dapat dikirimkan pada laporan berstatus '{$laporan->status_laporan}'.");
        }

        // 3. Mencegah disposisi ke unit kerja sendiri
        if ($request->unit_penerima_id == Auth::user()->unit_kerja_id) {
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

        // Buat data Disposisi Laporan
        $disposisi = DisposisiLaporan::create([
            'detail_laporan_id' => $laporan->id,
            'unit_pengirim_id'  => Auth::user()->unit_kerja_id,
            'petugas_disposisi_id'  => Auth::id(),
            'unit_penerima_id'  => $request->unit_penerima_id,
            'catatan_disposisi' => $request->catatan_disposisi,
            'status_disposisi'  => 'Pending',
            'tanggal_disposisi' => now(),
        ]);

        $unitPenerima = UnitKerja::find($request->unit_penerima_id);

        // Jika status laporan utama masih Diajukan, otomatis ubah menjadi Diproses
        if ($laporan->status_laporan === 'Diajukan') {
            $laporan->status_laporan = 'Diproses';
            if (!$laporan->petugas_penangan_id) {
                $laporan->petugas_penangan_id = Auth::id();
            }
            $laporan->save();
        }

        // Pencatatan Log ke Audit Trail / Riwayat Laporan
        RiwayatLaporan::create([
            'detail_laporan_id' => $laporan->id,
            'user_id'           => Auth::id(),
            'status_sebelumnya' => $laporan->status_laporan,
            'status_baru'       => $laporan->status_laporan,
            'keterangan'        => "Disposisi dikirim ke unit [{$unitPenerima->nama_unit}]: {$request->catatan_disposisi}",
        ]);

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
            'lampiran_file'         => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5048'],
        ], [
            'status_disposisi.required'      => 'Status disposisi wajib ditentukan.',
            'catatan_tindak_lanjut.required' => 'Catatan tindak lanjut wajib diisi.',
            'lampiran_file.max'              => 'Ukuran berkas lampiran maksimal 5MB.',
        ]);

        $disposisi = DisposisiLaporan::with(['detailLaporan', 'unitPenerima'])->findOrFail($id);

        // Unggah berkas bukti pengerjaan disposisi jika ada
        if ($request->hasFile('lampiran_file')) {
            $disposisi->lampiran_file = $request->file('lampiran_file')->store('lampiran_disposisi', 'public');
        }

        $disposisi->status_disposisi      = $request->status_disposisi;
        $disposisi->catatan_tindak_lanjut = $request->catatan_tindak_lanjut;
        $disposisi->petugas_disposisi_id      = Auth::id();

        if ($request->status_disposisi === 'Selesai') {
            $disposisi->tanggal_selesai = now();
        }

        $disposisi->save();

        // Pencatatan Log Audit Trail
        RiwayatLaporan::create([
            'detail_laporan_id' => $disposisi->detail_laporan_id,
            'user_id'           => Auth::id(),
            'status_sebelumnya' => $disposisi->detailLaporan->status_laporan,
            'status_baru'       => $disposisi->detailLaporan->status_laporan,
            'keterangan'        => "Disposisi [Unit {$disposisi->unitPenerima->nama_unit}] diperbarui menjadi '{$request->status_disposisi}': {$request->catatan_tindak_lanjut}",
        ]);

        return redirect()->back()
            ->with('success', "Status disposisi unit {$disposisi->unitPenerima->nama_unit} berhasil diperbarui.");
    }

    /**
     * Menghapus lembar disposisi (jika dibatalkan oleh unit pengirim).
     */
    public function destroy($id)
    {
        $disposisi = DisposisiLaporan::with('unitPenerima')->findOrFail($id);

        $laporanId = $disposisi->detail_laporan_id;
        $unitNama  = $disposisi->unitPenerima->nama_unit ?? 'Tujuan';

        // Hanya bisa dihapus jika statusnya masih Menunggu
        if ($disposisi->status_disposisi !== 'pending') {
            return redirect()->back()->with('error', 'Disposisi yang sudah diproses/selesai tidak dapat dibatalkan.');
        }

        $disposisi->delete();

        // Log Audit Trail
        RiwayatLaporan::create([
            'detail_laporan_id' => $laporanId,
            'user_id'           => Auth::id(),
            'status_sebelumnya' => null,
            'status_baru'       => null,
            'keterangan'        => "Disposisi ke unit [{$unitNama}] telah dibatalkan.",
        ]);

        return redirect()->back()->with('success', 'Disposisi berhasil dibatalkan/dihapus.');
    }
}
