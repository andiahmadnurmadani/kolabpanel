<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $activities = $user->activities()
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();

        return response()->json([
            'data' => $activities,
        ]);
    }

    public function show(Request $request, int $id)
    {
        $user = $request->user();
        $activity = $user->activities()->findOrFail($id);

        return response()->json([
            'data' => $activity,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'desk' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'status' => 'nullable|in:pending,selesai',
            'reminder' => 'nullable|string',
        ]);

        $activity = $user->activities()->create([
            'title' => $validated['title'],
            'desk' => $validated['desk'] ?? '',
            'date' => $validated['date'],
            'time' => $validated['time'] ?? '',
            'status' => $validated['status'] ?? 'pending',
            'reminder' => $validated['reminder'] ?? 'none',
        ]);

        return response()->json([
            'message' => 'Activity created',
            'data' => $activity,
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $user = $request->user();
        $activity = $user->activities()->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'desk' => 'nullable|string',
            'date' => 'nullable|date',
            'time' => 'nullable|date_format:H:i',
            'status' => 'nullable|in:pending,selesai',
            'reminder' => 'nullable|string',
        ]);

        $data = [
            'title' => $validated['title'] ?? $activity->title,
            'desk' => array_key_exists('desk', $validated)
                ? ($validated['desk'] ?? '')
                : $activity->desk,
            'date' => $validated['date'] ?? $activity->date,
            'time' => $validated['time'] ?? $activity->time,
            'status' => $validated['status'] ?? $activity->status,
            'reminder' => $validated['reminder'] ?? $activity->reminder,
        ];

        $activity->update($data);

        return response()->json([
            'message' => 'Activity updated',
            'data' => $activity->fresh(),
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $user = $request->user();
        $activity = $user->activities()->findOrFail($id);
        $activity->delete();

        return response()->json([
            'message' => 'Activity deleted',
        ]);
    }
}
