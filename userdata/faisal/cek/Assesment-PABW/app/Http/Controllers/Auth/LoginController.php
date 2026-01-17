<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check if user exists
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return redirect()->back()->withErrors([
                'email' => 'Email tidak ditemukan. Silakan periksa kembali email Anda.'
            ])->withInput($request->except('password'));
        }

        // Debug: Show user role before authentication
        \Log::info('User with email ' . $request->email . ' found with role: ' . $user->role);

        // Attempt to log the user in
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Reload the user to make sure we have fresh data
            $user = User::where('email', $request->email)->first();

            // Regenerate session to prevent fixation attacks
            $request->session()->regenerate();

            // Debug: Show user role after authentication
            \Log::info('Session regenerated. User role after authentication: ' . $user->role . ' and email: ' . $user->email);

            // Determine redirect path based on user role
            // Admin users should always go to admin dashboard after login
            if ($user->role === 'admin') {
                return redirect('/admin/dashboard');
            }

            // For non-admin users, check if there's an intended URL
            $intended = redirect()->intended(null);
            if ($intended && $intended !== url('/login')) {
                return $intended;
            }

            // Default redirect for non-admin users
            return redirect('/');
        }

        // If login fails due to incorrect password
        return back()->withErrors([
            'password' => 'Kata sandi yang Anda masukkan salah. Silakan coba lagi.',
        ])->withInput($request->except('password'));
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Get the post-login redirect path.
     *
     * @return string
     */
    protected function redirectTo()
    {
        // Redirect based on user role - we'll determine this in the login method after authentication
        // For now, return a default value that will be overridden in the login method
        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/';
    }
}