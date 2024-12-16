<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        return response()->json(Team::with(['coach', 'players'])->get(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'coach_id' => 'nullable|exists:coaches,id', // Asegura que el coach_id exista
        ]);

        $team = Team::create($validated);

        return response()->json($team, 201);
    }


    public function show($id)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json(['message' => 'Team not found'], 404);
        }

        return response()->json($team, 200);
    }

    public function update(Request $request, $id)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json(['message' => 'Team not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'coach_id' => 'nullable|exists:coaches,id', // Asegura que coach_id exista o sea null
        ]);

        $team->update($validated);

        return response()->json($team, 200);
    }

    public function destroy($id)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json(['message' => 'Team not found'], 404);
        }

        $team->delete();

        return response()->json(['message' => 'Team deleted'], 200);
    }

    public function leaderboard()
    {
        $teams = Team::orderBy('points', 'desc')
            ->orderBy('matches_won', 'desc')
            ->get(['name', 'matches_played', 'matches_won', 'matches_lost', 'points']);

        if ($teams->isEmpty()) {
            return response()->json(['leaderboard' => []], 200);
        }

        return response()->json(['leaderboard' => $teams], 200);
    }
}