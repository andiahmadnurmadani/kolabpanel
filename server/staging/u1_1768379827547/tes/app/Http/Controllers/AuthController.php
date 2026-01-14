<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\KontenStatis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    // -------------------------------
    // LOGIN
    // -------------------------------
    public function loginPage()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            // Check if user is blocked
            if (Auth::user()->blocked) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda telah diblokir. Silakan hubungi administrator.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            // Update last login
            Auth::user()->update(['last_login_at' => now()]);

            // Cek apakah user adalah admin
            if (Auth::user()->is_admin) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah!',
        ])->onlyInput('email');
    }

    // -------------------------------
    // REGISTER
    // -------------------------------
    public function registerPage()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard')->with('success', 'Registrasi berhasil!');
    }

    // -------------------------------
    // FORGOT PASSWORD
    // -------------------------------
    public function forgotPasswordPage()
    {
        return view('auth.forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email tidak ditemukan!');
        }

        // Simpan email untuk reset
        session(['reset_email' => $request->email]);

        return redirect('/change-password')->with('info', 'Email terverifikasi! Silakan ubah kata sandi.');
    }

    // -------------------------------
    // CHANGE PASSWORD
    // -------------------------------
    public function changePasswordPage()
    {
        // Jika user sudah login, langsung bisa akses
        if (Auth::check()) {
            return view('auth.change-password', ['fromProfile' => true]);
        }

        // Jika belum login, harus melalui forgot password dulu
        if (!session()->has('reset_email')) {
            return redirect('/forgot-password')->with('error', 'Harap verifikasi email dulu.');
        }

        return view('auth.change-password', ['fromProfile' => false]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        // Jika user sudah login, ubah password user yang login
        if (Auth::check()) {
            $user = Auth::user();
            
            // Validasi password lama jika dikirim
            if ($request->filled('current_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return back()->with('error', 'Password saat ini salah!');
                }
            }

            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            return redirect()->route('profile')->with('success', 'Kata sandi berhasil diubah!');
        }

        // Jika melalui forgot password
        $user = User::where('email', session('reset_email'))->first();

        if (!$user) {
            return redirect('/login')->with('error', 'User tidak ditemukan!');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        session()->forget('reset_email');

        return redirect('/login')->with('success', 'Kata sandi berhasil diubah!');
    }

    // -------------------------------
    // DASHBOARD
    // -------------------------------
    public function dashboard()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        $acaraMendatang = $user->activities()
            ->orderBy('date')
            ->orderBy('time')
            ->take(3)
            ->get();

        $semua_aktivitas = $user->activities()
            ->orderBy('date')
            ->get();

        // Ambil konten statis
        $kontenStatis = KontenStatis::pluck('value', 'key');

        return view('pages.dash', [
            'acaraMendatang' => $acaraMendatang,
            'semua_aktivitas' => $semua_aktivitas,
            'kontenStatis' => $kontenStatis,
        ]);
    }

    // -------------------------------
    // PROFIL USER
    // -------------------------------
    public function profile()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil konten statis
        $kontenStatis = KontenStatis::pluck('value', 'key');

        return view('pages.profile', compact('user', 'kontenStatis'));
    }

    // -------------------------------
    // LOGOUT
    // -------------------------------
    public function logout(Request $request)
    {
        Auth::logout();
        
        // Invalidate session dan regenerate token
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Clear semua session data
        $request->session()->flush();

        // Redirect dengan header no-cache untuk mencegah browser cache
        return redirect('/login')
            ->with('info', 'Kamu sudah logout.')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    // -------------------------------
    // UPDATE FOTO PROFIL
    // -------------------------------
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $user = Auth::user();

        // Hapus foto lama
        if ($user->profile_photo && Storage::exists($user->profile_photo)) {
            Storage::delete($user->profile_photo);
        }

        // Simpan foto baru
        $path = $request->file('profile_photo')->store('profile_photos', 'public');
        
        // Update database
        $user->update([
            'profile_photo' => $path
        ]);

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    // -------------------------------
    // HAPUS FOTO PROFIL
    // -------------------------------
    public function deletePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo && Storage::exists($user->profile_photo)) {
            Storage::delete($user->profile_photo);
        }

        $user->update([
            'profile_photo' => null
        ]);

        return back()->with('success', 'Foto profil dihapus!');
    }
}
