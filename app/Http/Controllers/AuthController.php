<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            // Cari user berdasarkan username
            $user = User::where('username', $credentials['username'])->first();
        } catch (\Exception $exception) {
            return back()->withErrors([
                'username' => 'Gagal terhubung ke database. Silakan coba lagi nanti.',
            ])->onlyInput('username');
        }

        if (! $user) {
            return back()->withErrors([
                'username' => 'Username tidak ditemukan',
            ])->onlyInput('username');
        }

        // Cek password
        if (! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'password' => 'Password salah',
            ])->onlyInput('username');
        }

        // Login user dan regenerasi session
        Auth::login($user);
        $request->session()->regenerate();

        // Redirect berdasarkan role
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'wali_kelas') {
            return redirect()->route('wali-kelas.dashboard');
        } elseif ($user->role === 'kakonsli') {
            return redirect()->route('kakonsli.dashboard');
        }

        return redirect()->route('siswa.dashboard');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
