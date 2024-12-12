<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index()
    {
        return response()->json(Tournament::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'allowed_hours_start' => 'required|date_format:H:i',
            'allowed_hours_end' => 'required|date_format:H:i|after:allowed_hours_start',
        ]);

        $tournament = Tournament::create($validated);

        return response()->json($tournament, 201);
    }

    public function show($id)
    {
        $tournament = Tournament::find($id);

        if (!$tournament) {
            return response()->json(['message' => 'Tournament not found'], 404);
        }

        return response()->json($tournament, 200);
    }

    public function update(Request $request, $id)
    {
        $tournament = Tournament::find($id);

        if (!$tournament) {
            return response()->json(['message' => 'Tournament not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'allowed_hours_start' => 'sometimes|required|date_format:H:i',
            'allowed_hours_end' => 'sometimes|required|date_format:H:i|after:allowed_hours_start',
        ]);

        $tournament->update($validated);

        return response()->json($tournament, 200);
    }

    public function destroy($id)
    {
        $tournament = Tournament::find($id);

        if (!$tournament) {
            return response()->json(['message' => 'Tournament not found'], 404);
        }

        $tournament->delete();

        return response()->json(['message' => 'Tournament deleted'], 200);
    }
}