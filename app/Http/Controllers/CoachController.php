<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use Illuminate\Http\Request;
use App\Models\Team;

class CoachController extends Controller
{
    public function index()
    {
        // Devuelve todos los entrenadores con los equipos asociados
        return response()->json(Coach::with('teams')->get(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:coaches',
            'phone' => 'nullable|string|max:15',
        ]);

        $coach = Coach::create($validated);

        return response()->json($coach, 201);
    }

    public function show($id)
    {
        // Buscar el entrenador con sus equipos asociados
        $coach = Coach::with('teams')->find($id);

        if (!$coach) {
            return response()->json(['message' => 'Coach not found'], 404);
        }

        return response()->json($coach, 200);
    }

    public function update(Request $request, $id)
    {
        $coach = Coach::find($id);

        if (!$coach) {
            return response()->json(['message' => 'Coach not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:coaches,email,' . $id,
            'phone' => 'nullable|string|max:15',
        ]);

        $coach->update($validated);

        return response()->json($coach, 200);
    }

    public function destroy($id)
    {
        $coach = Coach::find($id);

        if (!$coach) {
            return response()->json(['message' => 'Coach not found'], 404);
        }

        $coach->delete();

        return response()->json(['message' => 'Coach deleted'], 200);
    }
}