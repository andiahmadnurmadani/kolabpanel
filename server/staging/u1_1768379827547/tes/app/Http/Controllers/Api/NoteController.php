<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $notes = $user->notes()->latest()->get();

        return response()->json([
            'data' => $notes,
        ]);
    }

    public function show(Request $request, int $id)
    {
        $user = $request->user();
        $note = $user->notes()->findOrFail($id);

        return response()->json([
            'data' => $note,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'category' => 'nullable|string',
        ]);

        $note = $user->notes()->create([
            'title' => $validated['title'],
            'content' => $validated['content'] ?? '',
            'category' => $validated['category'] ?? 'personal',
        ]);

        return response()->json([
            'message' => 'Note created',
            'data' => $note,
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $user = $request->user();
        $note = $user->notes()->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'nullable|string',
            'category' => 'nullable|string',
        ]);

        $data = [
            'title' => $validated['title'] ?? $note->title,
            'content' => array_key_exists('content', $validated)
                ? ($validated['content'] ?? '')
                : $note->content,
            'category' => $validated['category'] ?? $note->category,
        ];

        $note->update($data);

        return response()->json([
            'message' => 'Note updated',
            'data' => $note->fresh(),
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $user = $request->user();
        $note = $user->notes()->findOrFail($id);
        $note->delete();

        return response()->json([
            'message' => 'Note deleted',
        ]);
    }
}
