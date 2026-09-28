<?php

namespace App\Http\Controllers;

use App\Models\MasterLaporan;
use Illuminate\Http\Request;

class MasterLaporanController extends Controller
{
    public function index()
    {
        $masterLaporanList = MasterLaporan::withCount('detailLaporan')->get();
        return view('master.master_laporan', compact('masterLaporanList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_laporan'  => ['required', 'string', 'max:255'],
            'jenis_laporan' => ['required', 'in:Rutin,Insidentil'],
            'deskripsi'     => ['nullable', 'string'],
        ], [
            'nama_laporan.required'  => 'Nama kategori laporan wajib diisi.',
            'jenis_laporan.required' => 'Jenis laporan (Rutin/Insidentil) wajib dipilih.',
        ]);

        MasterLaporan::create([
            'nama_laporan'  => $request->nama_laporan,
            'jenis_laporan' => $request->jenis_laporan,
            'deskripsi'     => $request->deskripsi,
            'status_aktif'  => $request->has('status_aktif') ? true : false,
        ]);

        return redirect()->back()->with('success', 'Master Kategori Laporan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $master = MasterLaporan::findOrFail($id);

        $request->validate([
            'nama_laporan'  => ['required', 'string', 'max:255'],
            'jenis_laporan' => ['required', 'in:Rutin,Insidentil'],
            'deskripsi'     => ['nullable', 'string'],
        ]);

        $master->update([
            'nama_laporan'  => $request->nama_laporan,
            'jenis_laporan' => $request->jenis_laporan,
            'deskripsi'     => $request->deskripsi,
            'status_aktif'  => $request->has('status_aktif') ? true : false,
        ]);

        return redirect()->back()->with('success', 'Master Kategori Laporan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $master = MasterLaporan::findOrFail($id);

        if ($master->detailLaporan()->count() > 0) {
            // Jika sudah dipakai transaksi, ubah status non-aktif saja
            $master->update(['status_aktif' => false]);
            return redirect()->back()->with('success', 'Kategori laporan dinonaktifkan karena memiliki riwayat transaksi.');
        }

        $master->delete();
        return redirect()->back()->with('success', 'Master Kategori Laporan berhasil dihapus.');
    }
}
