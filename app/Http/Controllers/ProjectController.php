<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request, Team $team)
    {
        if (! $team->users()->where('users.id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $team->projects()->get();
    }

    public function store(Request $request, Team $team)
    {
        if (! $team->users()->where('users.id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $project = $team->projects()->create($data);

        return response()->json($project, 201);
    }

    public function update(Request $request, Project $project)
    {
        if (! $request->user()->teams()->where('teams.id', $project->team_id)->exists()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $project->update($data);

        return $project;
    }

    public function destroy(Request $request, Project $project)
    {
        if (! $request->user()->teams()->where('teams.id', $project->team_id)->exists()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $project->delete();

        return response()->noContent();
    }
}