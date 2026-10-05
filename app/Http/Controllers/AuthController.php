<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            $activeRole = session('active_role', Auth::user()->role);
            return $this->redirectBasedOnRole($activeRole);
        }
        return view('auth.login');
    }

    /**
     * Handle authentication.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            session(['active_role' => $user->role]);

            return $this->redirectBasedOnRole($user->role);
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        return redirect()->route('login')->with('info', 'Registrasi mandiri tidak tersedia. Akun karyawan didaftarkan oleh HR.');
    }

    /**
     * Handle registration.
     */
    public function register(Request $request)
    {
        return redirect()->route('login')->with('info', 'Registrasi mandiri tidak tersedia. Akun karyawan didaftarkan oleh HR.');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Helper to redirect based on role.
     */
    private function redirectBasedOnRole($role)
    {
        if ($role === 'karyawan') {
            return redirect()->route('karyawan.dashboard');
        } elseif ($role === 'admin' || $role === 'hr') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'manajer') {
            return redirect()->route('manajer.dashboard');
        }

        return redirect('/');
    }
}
