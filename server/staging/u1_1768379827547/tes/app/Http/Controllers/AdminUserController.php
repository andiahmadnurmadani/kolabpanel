<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\KontenStatis;

class AdminUserController extends Controller
{
    public function index(Request $req)
    {
        $q = $req->query('q');
        $users = User::when($q, fn($qb) => $qb->where('name', 'like', '%' . $q . '%')->orWhere('email', 'like', '%' . $q . '%'))
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        $kontenStatis = KontenStatis::pluck('value', 'key');
        return view('admin.user.index', compact('users', 'q', 'kontenStatis'));
    }

    public function edit(User $user)
    {
        $kontenStatis = KontenStatis::pluck('value', 'key');
        return view('admin.user.edit', compact('user', 'kontenStatis'));
    }

    public function update(Request $req, User $user)
    {
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email,' . $user->id,
            'is_admin' => 'nullable|boolean',
            'blocked' => 'nullable|boolean'
        ]);
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'is_admin' => $req->has('is_admin') ? (bool) $req->is_admin : false,
            'blocked' => $req->has('blocked') ? (bool) $req->blocked : false,
        ]);
        return redirect()->route('admin.users.index')->with('success', 'User updated');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted');
    }
}
