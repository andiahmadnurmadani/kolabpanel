<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function loginRegister()
    {
        return view('login-register');
    }

    public function loginProcess(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('shop')
                ->with('success', 'Login berhasil!');
        }

        return back()->with('error', 'Email atau password salah!');
    }

    /**
     * Process register
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'              => 'required',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|min:5',
            'security_question' => 'required',
            'security_answer'   => 'required'
        ]);

        User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => $request->password,  // otomatis hashed krn cast
            'security_question' => $request->security_question,
            'security_answer'   => $request->security_answer,
            'role'              => 'customer'
        ]);

        return redirect()->route('login-register.page')
            ->with('success', 'Akun berhasil dibuat! Silakan login.');
    }

    /**
     * Logout using Laravel Auth
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login-register.page')
            ->with('success', 'Kamu sudah logout.');
    }
}