<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user sudah login
        if (!session()->has('logged_in_user_email')) {
            return redirect()->route('login-register.page')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil user dari database
        $user = \App\Models\User::where('email', session('logged_in_user_email'))->first();

        // Jika user tidak ditemukan / bukan admin
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Kamu tidak memiliki akses ke halaman admin.');
        }

        return $next($request);
    }
}