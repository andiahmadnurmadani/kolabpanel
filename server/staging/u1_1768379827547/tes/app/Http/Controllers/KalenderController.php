<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\KontenStatis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KalenderController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Ambil semua aktivitas user, sorted by date
        $allActivities = Auth::user()->activities()->orderBy('date', 'asc')->get();
        $kontenStatis = KontenStatis::pluck('value', 'key');

        return view('pages.kalendessr', [
            'aktivitasBulanIni' => $allActivities,
            'kontenStatis' => $kontenStatis,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'desk' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'status' => 'nullable|in:pending,selesai',
        ]);

        $data = [
            'title' => $validated['title'],
            'desk' => $validated['desk'] ?? '',
            'date' => $validated['date'],
            'time' => $validated['time'] ?? '',
            'status' => $validated['status'] ?? 'pending',
            'reminder' => 'none',
        ];

        Auth::user()->activities()->create($data);

        return redirect()->route('kalender.index')->with('success', 'Acara berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'desk' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'status' => 'nullable|in:pending,selesai',
        ]);

        $activity = Auth::user()->activities()->findOrFail($id);
        
        $activity->update([
            'title' => $validated['title'],
            'desk' => $validated['desk'] ?? '',
            'date' => $validated['date'],
            'time' => $validated['time'] ?? '',
            'status' => $validated['status'] ?? 'pending',
        ]);

        return redirect()->route('kalender.index')->with('success', 'Acara berhasil diperbarui');
    }

    public function destroy($id)
    {
        $activity = Auth::user()->activities()->findOrFail($id);
        $activity->delete();

        return redirect()->route('kalender.index')->with('success', 'Acara berhasil dihapus');
    }
}
