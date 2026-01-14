<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\KontenStatis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatatanController extends Controller
{
    // Menampilkan halaman catatan
    public function index()
    {
        $notes = Auth::user()->notes()->latest()->get();
        $kontenStatis = KontenStatis::pluck('value', 'key');
        return view('pages.catatan', compact('notes', 'kontenStatis'));
    }

    // Menyimpan catatan baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'nullable|string',
        ]);

        Auth::user()->notes()->create([
            'title' => $validated['judul'],
            'content' => $validated['isi'] ?? '',
        ]);

        return redirect('/catatan')->with('success', 'Catatan berhasil ditambahkan!');
    }

    // Update catatan
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'nullable|string',
        ]);

        $note = Auth::user()->notes()->findOrFail($id);
        $note->update([
            'title' => $validated['judul'],
            'content' => $validated['isi'] ?? '',
        ]);

        return redirect('/catatan')->with('success', 'Catatan berhasil diperbarui!');
    }

    // Hapus catatan
     
    public function destroy($id)
    {
        $note = Auth::user()->notes()->findOrFail($id);
        $note->delete();

        return redirect('/catatan')->with('info', 'Catatan dihapus.');
    }
}
