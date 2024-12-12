<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index()
    {
        return response()->json(Player::with(['team.coach'])->get(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'team_id' => 'nullable|exists:teams,id',
            'position' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:10|max:100',
            'ci_number' => 'required|string|unique:players,ci_number',
        ]);

        $player = Player::create($validated);

        return response()->json($player, 201);
    }

    public function show($id)
    {
        $player = Player::with('team')->find($id);

        if (!$player) {
            return response()->json(['message' => 'Player not found'], 404);
        }

        return response()->json($player, 200);
    }

    public function update(Request $request, $id)
    {
        $player = Player::find($id);

        if (!$player) {
            return response()->json(['message' => 'Player not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'team_id' => 'nullable|exists:teams,id',
            'position' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:10|max:100',
            'ci_number' => 'sometimes|required|string|unique:players,ci_number,' . $id,
        ]);

        $player->update($validated);

        return response()->json($player, 200);
    }

    public function destroy($id)
    {
        $player = Player::find($id);

        if (!$player) {
            return response()->json(['message' => 'Player not found'], 404);
        }

        $player->delete();

        return response()->json(['message' => 'Player deleted'], 200);
    }
}