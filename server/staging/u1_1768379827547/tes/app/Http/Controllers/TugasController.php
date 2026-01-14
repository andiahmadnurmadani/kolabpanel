<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\KontenStatis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $tasks = $user->tasks()->latest()->get();
        $kontenStatis = KontenStatis::pluck('value', 'key');
        return view('pages.kelola-tugas', compact('tasks', 'kontenStatis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'detail_tugas' => 'nullable|string',
            'matkul' => 'nullable|string|max:255',
            'tenggat' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high',
            'status' => 'nullable|in:pending,completed',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->tasks()->create([
            'title' => $validated['nama_tugas'],
            'description' => $validated['detail_tugas'] ?? '',
            'due_date' => $validated['tenggat'] ?? now()->addWeek(),
            'priority' => $validated['priority'] ?? 'medium',
            'status' => $validated['status'] ?? 'pending',
        ]);

        return redirect()->back()->with('success', 'Tugas berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'detail_tugas' => 'nullable|string',
            'matkul' => 'nullable|string|max:255',
            'tenggat' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high',
            'status' => 'required|in:pending,completed',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $task = $user->tasks()->findOrFail($id);
        
        $data = [
            'title' => $validated['nama_tugas'],
            'description' => $validated['detail_tugas'] ?? '',
            'due_date' => $validated['tenggat'] ?? $task->due_date,
            'priority' => $validated['priority'] ?? $task->priority,
            'status' => $validated['status'],
        ];

        if ($validated['status'] === 'completed' && !$task->isCompleted()) {
            $data['completed_at'] = now();
        } elseif ($validated['status'] === 'pending') {
            $data['completed_at'] = null;
        }

        $task->update($data);

        return redirect()->back()->with('success', 'Tugas berhasil diperbarui');
    }

    public function destroy($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $task = $user->tasks()->findOrFail($id);
        $task->delete();

        return redirect()->back()->with('success', 'Tugas berhasil dihapus');
    }

    public function complete($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $task = $user->tasks()->findOrFail($id);
        $task->markAsCompleted();

        return redirect()->back()->with('success', 'Tugas ditandai selesai');
    }
}
