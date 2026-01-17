<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotifikasiController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || auth()->user()->role !== 'admin') {
                abort(403, 'Akses ditolak. Anda bukan admin.');
            }
            return $next($request);
        });
    }

    // 🟦 Tampilkan semua notifikasi
    public function index()
    {
        $notifications = Notification::orderBy('created_at', 'desc')->get();
        return view('admin.notifications.index', compact('notifications'));
    }

    // 🟩 Form tambah notifikasi
    public function create()
    {
        return view('admin.notifications.create');
    }

    // 🟨 Simpan notifikasi baru
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'message' => 'required'
        ]);

        Notification::create([
            'title' => $request->title,
            'message' => $request->message,
        ]);

        return redirect()->route('admin.notifications.index')
                         ->with('success', 'Notifikasi berhasil ditambahkan!');
    }

    // 🟧 Form edit notifikasi
    public function edit($id)
    {
        $notification = Notification::findOrFail($id);

        return view('admin.notifications.edit', compact('notification'));
    }

    // 🟪 Update notifikasi
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'message' => 'required'
        ]);

        $notification = Notification::findOrFail($id);
        $notification->update([
            'title' => $request->title,
            'message' => $request->message,
        ]);

        return redirect()->route('admin.notifications.index')
                         ->with('success', 'Notifikasi berhasil diperbarui!');
    }

    // 🟥 Hapus notifikasi
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return redirect()->route('admin.notifications.index')
                         ->with('success', 'Notifikasi berhasil dihapus!');
    }
}
