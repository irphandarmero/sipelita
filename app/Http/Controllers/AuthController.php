<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman/form login SI-PELITA.
     */
    public function showLoginForm()
    {
        // Jika user sudah login, langsung arahkan ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Memproses otentikasi/login pengguna.
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'login'    => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ], [
            'login.required'    => 'Email atau username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $login = trim($credentials['login']);
        $loginField = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $loginValue = $loginField === 'username' ? strtolower($login) : $login;

        // 2. Coba autentikasi menggunakan email atau username
        $remember = $request->has('remember');

        if (Auth::attempt([
            $loginField => $loginValue,
            'password' => $credentials['password'],
        ], $remember)) {
            // Regenerasi session untuk keamanan (mencegah Session Fixation attack)
            $request->session()->regenerate();

            $user = Auth::user();
            $namaUser = $user->name;
            $namaUnit = $user->unitKerja->nama_unit ?? 'Unit Kerja';

            return redirect()->intended(route('dashboard'))
                ->with('success', "Selamat datang kembali, {$namaUser} ({$namaUnit})!");
        }

        // 3. Jika autentikasi gagal
        return back()->withErrors([
            'login' => 'Email/username atau password yang Anda masukkan salah.',
        ])->onlyInput('login');
    }

    /**
     * Memproses keluar sistem (logout).
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidasi session & regenerasi CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem SI-PELITA.');
    }

    /**
     * Menampilkan form ubah password pengguna.
     */
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * Memproses pembaruan password pengguna yang sedang login.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.min'              => 'Password baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi password baru tidak cocok.',
            'password.different'        => 'Password baru harus berbeda dengan password saat ini.',
        ]);

        $user = Auth::user();

        // Periksa apakah password saat ini benar
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai.',
            ]);
        }

        // Perbarui password dengan enkripsi Hash
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->back()->with('success', 'Password Anda berhasil diperbarui!');
    }
}
