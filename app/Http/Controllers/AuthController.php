<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk facade Auth
use Illuminate\Support\Facades\Session; // Untuk flash messages
use Illuminate\Support\Facades\Hash; // Untuk hashing password
use App\Models\User; // Import model User

class AuthController extends Controller
{
    /**
     * Menampilkan form login.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // Jika user sudah login, arahkan ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard'); // Sesuaikan dengan nama rute dashboard kamu
        }
        return view('auth.login');
    }

    /**
     * Memproses permintaan login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Secara default, Laravel akan mencoba autentikasi menggunakan kolom 'email' dan 'password'.
        // Karena kita pakai 'username' dan 'password_hash', kita harus cari user secara manual
        // dan membandingkan passwordnya.

        $user = User::where('username', $credentials['username'])->first();

        // Perbaikan: Tambahkan pengecekan is_active
        if ($user) {
            if (Hash::check($credentials['password'], $user->password_hash)) {
                if ($user->is_active) { // Cek apakah user aktif
                    // Jika user ditemukan, password cocok, dan user aktif
                    Auth::login($user, $request->has('remember')); // Login user, dengan opsi "remember me"

                    // Regenerate session untuk mencegah session fixation attacks
                    $request->session()->regenerate();

                    Session::flash('success', 'Selamat datang, ' . $user->nama_lengkap . '!');
                    return redirect()->intended(route('dashboard')); // Arahkan ke URL yang ingin diakses sebelumnya, atau ke dashboard
                } else {
                    // User tidak aktif
                    Session::flash('error', 'Akun Anda tidak aktif. Silakan hubungi administrator.');
                    return redirect()->back()->withInput($request->only('username'));
                }
            } else {
                // Password salah
                Session::flash('error', 'Username atau password salah.');
                return redirect()->back()->withInput($request->only('username'));
            }
        } else {
            // User tidak ditemukan
            Session::flash('error', 'Username atau password salah.');
            return redirect()->back()->withInput($request->only('username'));
        }
    }

    /**
     * Melakukan logout user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout(); // Logout user

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        Session::flash('success', 'Anda telah berhasil logout.');
        return redirect('/login'); // Redirect ke halaman login setelah logout
    }
}
