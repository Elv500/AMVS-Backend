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
            'coach_id' => 'nullable|exists:coaches,id',
            'logo' => 'nullable|image|max:2048', // Validar la imagen (hasta 2MB)
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $team = Team::create($validated);

        return response()->json($team, 201);
    }

    public function show($id)
    {
        // Buscar el equipo con las relaciones coach y players
        $team = Team::with(['coach', 'players'])->find($id);
    
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

        // Validar los campos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'coach_id' => 'nullable|exists:coaches,id',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048', // Validación de logo
        ]);

        // Actualizar el nombre y coach_id
        $team->name = $validated['name'];
        if (isset($validated['coach_id'])) {
            $team->coach_id = $validated['coach_id']; // Asignar entrenador
        } else {
            $team->coach_id = null; // Permitir desasignar entrenador
        }

        // Verificar si se subió un nuevo logo
        if ($request->hasFile('logo')) {
            // Eliminar el logo anterior si existe
            if ($team->logo) {
                $oldLogoPath = str_replace('storage/', 'public/', $team->logo);
                if (\Storage::exists($oldLogoPath)) {
                    \Storage::delete($oldLogoPath);
                }
            }

            // Subir y guardar el nuevo logo
            $filePath = $request->file('logo')->store('logos', 'public');
            $team->logo = $filePath;
        }

        $team->save();

        // Devolver el equipo con sus relaciones actualizadas
        return response()->json($team->load(['coach', 'players']), 200);
    }

    public function destroy($id)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json(['message' => 'Team not found'], 404);
        }

        // Eliminar el archivo del logo si existe
        if ($team->logo && \Storage::disk('public')->exists($team->logo)) {
            \Storage::disk('public')->delete($team->logo);
        }

        // Eliminar el equipo de la base de datos
        $team->delete();

        return response()->json(['message' => 'Team deleted successfully'], 200);
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