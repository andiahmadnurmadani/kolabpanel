<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class AccountController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // TEMPORARY dummy orders (karena belum ada tabel transaksi)
        $orders = [
            (object)[
                'kode_transaksi' => 'BW123',
                'created_at' => now()->subDays(3),
                'status' => 'delivered',
                'total_harga' => 250000,
            ],
            (object)[
                'kode_transaksi' => 'BW456',
                'created_at' => now()->subDays(7),
                'status' => 'pending',
                'total_harga' => 175000,
            ],
        ];

        return view('accounts', compact('user', 'orders'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->hasFile('picture')) {
            // Hapus foto lama jika ada
            if ($user->picture && Storage::disk('public')->exists($user->picture)) {
                Storage::disk('public')->delete($user->picture);
            }

            // Simpan foto baru
            $path = $request->file('picture')->store('profile-pictures', 'public');
            $data['picture'] = $path;
        }

        $user->update($data);

        return redirect()
            ->route('accounts')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    public function updateAddress(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->update([
            'address' => $request->address,
        ]);

        return redirect()
            ->route('accounts')
            ->with('success', 'Alamat berhasil disimpan!');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:5',
            'confirm_password' => 'required|min:5|same:new_password',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()
                ->route('accounts')
                ->with('error', 'Password saat ini salah!');
        }

        // Password otomatis di-hash oleh model cast
        $user->update([
            'password' => $request->new_password,
        ]);

        return redirect()
            ->route('accounts')
            ->with('success', 'Password berhasil diubah!');
    }

    public function deleteAccount(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->delete();

        Auth::logout();

        return redirect()
            ->route('login-register.page')
            ->with('success', 'Akun berhasil dihapus!');
    }
}
