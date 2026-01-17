<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // 1. Tampilkan Daftar User
    public function index(Request $request)
    {
        $query = User::query();

        // Fitur Pencarian
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Urutkan dari yang terbaru
        $users = $query->latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    // 2. Tampilkan Detail User & Riwayat
    public function show($id)
    {
        $user = User::with(['donations', 'volunteerApplications.campaign'])->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    // 3. Update Data User
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:donatur,admin', // Sesuaikan role yang ada (matches database enum)
            'password' => 'nullable|min:8', // Password opsional
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Jika password diisi, hash dan update
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Profil pengguna berhasil diperbarui.');
    }

    // 4. Hapus User
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.profiles.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}