<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }
    public function register(Request $request)
    {
        // 1. VALIDASI: Hapus 'role' dan 'terms' dari aturan validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            // 'role' => 'required|in:donatur',  <-- HAPUS INI (Input tidak ada di form)
            // 'terms' => 'required',            <-- HAPUS INI (Input tidak ada di form)
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput(); // Agar input nama/email tidak hilang saat error
        }

        // 2. CREATE USER: Set role manual sebagai 'donatur'
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'donatur', // <--- SET MANUAL DISINI (Hardcoded)
        ]);

        // 3. AUTO LOGIN
        auth()->login($user);

        // 4. REDIRECT
        return redirect('/')->with('success', 'Pendaftaran berhasil! Selamat datang di DonGiv.');
    }
}