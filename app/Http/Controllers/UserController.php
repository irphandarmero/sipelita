<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
            'username'      => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('users', 'username')],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'      => ['required', 'string', 'min:6'],
            'unit_kerja_id' => ['required', 'exists:unit_kerja,id'],
            'role'          => ['required', 'in:admin,petugas,kepala_unit,direksi'],
        ], [
            'name.required'          => 'Nama lengkap wajib diisi.',
            'email.required'         => 'Alamat email wajib diisi.',
            'email.unique'           => 'Alamat email sudah terdaftar.',
            'username.required'      => 'Username wajib diisi.',
            'username.regex'         => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung.',
            'username.unique'        => 'Username sudah digunakan.',
            'password.required'      => 'Password wajib diisi.',
            'unit_kerja_id.required' => 'Unit kerja wajib dipilih.',
            'role.required'          => 'Role akses pengguna wajib dipilih.',
        ]);

        User::create([
            'name'          => $request->name,
            'username'      => strtolower(trim($request->username)),
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
            'username'      => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('users', 'username')->ignore($user->id)],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'unit_kerja_id' => ['required', 'exists:unit_kerja,id'],
            'role'          => ['required', 'in:admin,petugas,kepala_unit,direksi'],
            'password'      => ['nullable', 'string', 'min:6'],
        ]);

        $dataUpdate = [
            'name'          => $request->name,
            'username'      => $request->filled('username') ? strtolower(trim($request->username)) : null,
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
