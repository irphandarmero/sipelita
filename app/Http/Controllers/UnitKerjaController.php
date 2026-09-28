<?php

namespace App\Http\Controllers;

use App\Models\UnitKerja;
use Illuminate\Http\Request;

class UnitKerjaController extends Controller
{
    public function index()
    {
        $unitKerjaList = UnitKerja::withCount(['users', 'laporanAsal', 'laporanTujuan'])->get();
        return view('master.unit_kerja', compact('unitKerjaList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_unit' => ['required', 'string', 'max:255'],
            'kode_unit' => ['required', 'string', 'max:50', 'unique:unit_kerja,kode_unit'],
        ], [
            'nama_unit.required' => 'Nama unit kerja wajib diisi.',
            'kode_unit.required' => 'Kode unit kerja wajib diisi.',
            'kode_unit.unique'   => 'Kode unit kerja sudah digunakan.',
        ]);

        UnitKerja::create([
            'nama_unit' => $request->nama_unit,
            'kode_unit' => strtoupper($request->kode_unit),
        ]);

        return redirect()->back()->with('success', 'Unit Kerja baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $unit = UnitKerja::findOrFail($id);

        $request->validate([
            'nama_unit' => ['required', 'string', 'max:255'],
            'kode_unit' => ['required', 'string', 'max:50', 'unique:unit_kerja,kode_unit,' . $id],
        ]);

        $unit->update([
            'nama_unit' => $request->nama_unit,
            'kode_unit' => strtoupper($request->kode_unit),
        ]);

        return redirect()->back()->with('success', 'Data Unit Kerja berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $unit = UnitKerja::findOrFail($id);

        if ($unit->users()->count() > 0 || $unit->laporanAsal()->count() > 0 || $unit->laporanTujuan()->count() > 0) {
            return redirect()->back()->with('error', 'Unit Kerja tidak dapat dihapus karena masih terhubung dengan data User atau Laporan.');
        }

        $unit->delete();
        return redirect()->back()->with('success', 'Unit Kerja berhasil dihapus.');
    }
}
