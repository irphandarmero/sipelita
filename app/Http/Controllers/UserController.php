<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $usersList = User::with('unitKerja')->latest()->get();
        $unitKerjaList = UnitKerja::all();
        return view('master.users', compact('usersList', 'unitKerjaList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'      => ['required', 'string', 'min:6'],
            'unit_kerja_id' => ['required', 'exists:unit_kerja,id'],
            'role'          => ['required', 'in:admin,petugas,kepala_unit'],
        ], [
            'name.required'          => 'Nama lengkap wajib diisi.',
            'email.required'         => 'Alamat email wajib diisi.',
            'email.unique'           => 'Alamat email sudah terdaftar.',
            'password.required'      => 'Password wajib diisi.',
            'unit_kerja_id.required' => 'Unit kerja wajib dipilih.',
            'role.required'          => 'Role akses pengguna wajib dipilih.',
        ]);

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'unit_kerja_id' => $request->unit_kerja_id,
            'role'          => $request->role,
        ]);

        return redirect()->back()->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'unit_kerja_id' => ['required', 'exists:unit_kerja,id'],
            'role'          => ['required', 'in:admin,petugas,kepala_unit'],
            'password'      => ['nullable', 'string', 'min:6'],
        ]);

        $dataUpdate = [
            'name'          => $request->name,
            'email'         => $request->email,
            'unit_kerja_id' => $request->unit_kerja_id,
            'role'          => $request->role,
        ];

        if ($request->filled('password')) {
            $dataUpdate['password'] = Hash::make($request->password);
        }

        $user->update($dataUpdate);

        return redirect()->back()->with('success', 'Data Pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
