<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->role !== 'Admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return User::all();
    }

    public function update(Request $request, User $user)
    {
        if (auth()->user()->role !== 'Admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'role' => 'required|in:Admin,Manager,Member',
        ]);

        $user->update($data);

        return $user;
    }

    public function destroy(User $user)
    {
        if (auth()->user()->role !== 'Admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user->delete();

        return response()->noContent();
    }
}