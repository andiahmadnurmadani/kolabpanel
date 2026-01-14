<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $tasks = $user->tasks()->latest()->get();

        return response()->json([
            'data' => $tasks,
        ]);
    }

    public function show(Request $request, int $id)
    {
        $user = $request->user();
        $task = $user->tasks()->findOrFail($id);

        return response()->json([
            'data' => $task,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high',
            'status' => 'nullable|in:pending,completed',
        ]);

        $task = $user->tasks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'due_date' => $validated['due_date'] ?? null,
            'priority' => $validated['priority'] ?? 'medium',
            'status' => $validated['status'] ?? 'pending',
        ]);

        return response()->json([
            'message' => 'Task created',
            'data' => $task,
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $user = $request->user();
        $task = $user->tasks()->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high',
            'status' => 'nullable|in:pending,completed',
        ]);

        $data = [
            'title' => $validated['title'] ?? $task->title,
            'description' => array_key_exists('description', $validated)
                ? ($validated['description'] ?? '')
                : $task->description,
            'due_date' => $validated['due_date'] ?? $task->due_date,
            'priority' => $validated['priority'] ?? $task->priority,
            'status' => $validated['status'] ?? $task->status,
        ];

        if (($data['status'] ?? $task->status) === 'completed' && ! $task->isCompleted()) {
            $data['completed_at'] = now();
        } elseif (($data['status'] ?? $task->status) === 'pending') {
            $data['completed_at'] = null;
        }

        $task->update($data);

        return response()->json([
            'message' => 'Task updated',
            'data' => $task->fresh(),
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $user = $request->user();
        $task = $user->tasks()->findOrFail($id);
        $task->delete();

        return response()->json([
            'message' => 'Task deleted',
        ]);
    }

    public function complete(Request $request, int $id)
    {
        $user = $request->user();
        $task = $user->tasks()->findOrFail($id);
        $task->markAsCompleted();

        return response()->json([
            'message' => 'Task marked as completed',
            'data' => $task->fresh(),
        ]);
    }
}
