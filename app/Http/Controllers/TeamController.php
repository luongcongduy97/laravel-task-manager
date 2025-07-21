<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamController extends Controller
{

  public function index(Request $request)
  {
    if ($request->user()->role !== 'Admin') {
      return response()->json(['message' => 'Forbidden'], 403);
    }

    return Team::all();
  }

  public function store(Request $request)
  {
    if ($request->user()->role !== 'Admin') {
      return response()->json(['message' => 'Forbidden'], 403);
    }

    $data = $request->validate([
      'name' => ['required', 'string', 'max:255'],
    ]);

    $team = Team::create($data);

    return response()->json($team, 201);
  }

  public function invite(Request $request, Team $team)
  {
    if (! $team->users()->where('users.id', $request->user()->id)->exists()) {
      return response()->json(['message' => 'Forbidden'], 403);
    }
    $data = $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'email'],
    ]);

    $user = User::firstOrCreate(
      ['email' => $data['email']],
      [
          'name' => $data['name'],
          'password' => Str::password(),
          'role' => 'Member',
      ]
    );

    $team->users()->syncWithoutDetaching($user->id);

    return response()->json($user, 201);
  }
}