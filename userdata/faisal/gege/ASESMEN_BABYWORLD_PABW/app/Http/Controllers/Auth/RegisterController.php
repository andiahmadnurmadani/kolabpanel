<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class RegisterController extends Controller
{
    public function process(Request $request)
    {
        // Validate input
        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|min:5',
            'security_question' => 'required|string',
            'security_answer'   => 'required|string',
        ]);

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'address'           => $request->address,  // <-- WAJIB BENAR
            'security_question' => $request->security_question,
            'security_answer'   => $request->security_answer,
            'role'              => 'customer',
        ]);


        Auth::login($user);

        return redirect()->route('login-register.page')
            ->with('success', 'Akun berhasil dibuat! Silakan login.');
    }
}
