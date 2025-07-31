<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request, Project $project)
    {
        if (! $request->user()->teams()->where('teams.id', $project->team_id)->exists()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $project->tasks()->get();
    }

    public function store(Request $request, Project $project)
    {
        if (! $request->user()->teams()->where('teams.id', $project->team_id)->exists()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'string', 'max:255'],
            'due_date' => ['required', 'date'],
        ]);

        $task = $project->tasks()->create($data);

        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        if (! $request->user()->teams()->where('teams.id', $task->project->team_id)->exists()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'string', 'max:255'],
            'priority' => ['sometimes', 'required', 'string', 'max:255'],
            'due_date' => ['sometimes', 'required', 'date'],
        ]);

        $task->update($data);

        return $task;
    }

    public function destroy(Request $request, Task $task)
    {
        if (! $request->user()->teams()->where('teams.id', $task->project->team_id)->exists()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $task->delete();

        return response()->noContent();
    }
}