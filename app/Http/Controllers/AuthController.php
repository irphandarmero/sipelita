<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // 2. Coba autentikasi pengguna
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
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
            'email' => 'Alamat email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
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
}
