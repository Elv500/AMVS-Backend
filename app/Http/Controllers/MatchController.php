<?php

namespace App\Http\Controllers;

use App\Models\Match;
use App\Models\Team;
use App\Models\Field;
use App\Models\Tournament;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function index()
    {
        return response()->json(Match::with(['localTeam', 'visitorTeam', 'field', 'tournament'])->get(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'local_team_id' => 'required|exists:teams,id',
            'visitor_team_id' => 'required|exists:teams,id|different:local_team_id',
            'field_id' => 'required|exists:fields,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'tournament_id' => 'required|exists:tournaments,id', // Validar torneo
        ]);

        $match = Match::create($validated);

        return response()->json($match, 201);
    }

    public function show($id)
    {
        $match = Match::with(['localTeam', 'visitorTeam', 'field'])->find($id);

        if (!$match) {
            return response()->json(['message' => 'Match not found'], 404);
        }

        return response()->json($match, 200);
    }

    public function update(Request $request, $id)
    {
        $match = Match::find($id);

        if (!$match) {
            return response()->json(['message' => 'Match not found'], 404);
        }

        $validated = $request->validate([
            'local_team_id' => 'sometimes|required|exists:teams,id',
            'visitor_team_id' => 'sometimes|required|exists:teams,id|different:local_team_id',
            'field_id' => 'sometimes|required|exists:fields,id',
            'date' => 'sometimes|required|date',
            'time' => 'sometimes|required|date_format:H:i',
            'tournament_id' => 'sometimes|required|exists:tournaments,id', // Validar torneo
        ]);

        $match->update($validated);

        return response()->json($match, 200);
    }

    public function destroy($id)
    {
        $match = Match::find($id);

        if (!$match) {
            return response()->json(['message' => 'Match not found'], 404);
        }

        $match->delete();

        return response()->json(['message' => 'Match deleted'], 200);
    }

    public function updateState(Request $request, $id)
    {
        $match = Match::with(['localTeam', 'visitorTeam'])->find($id);

        if (!$match) {
            return response()->json(['message' => 'Match not found'], 404);
        }

        $validated = $request->validate([
            'state' => 'required|string|in:Pendiente,En curso,Concluido',
        ]);

        if ($validated['state'] === 'Concluido') {
            // Verificar que los sets estén registrados
            if (empty($match->sets)) {
                return response()->json(['message' => 'Cannot conclude a match without sets'], 422);
            }

            // Calcular sets ganados
            $localSets = 0;
            $visitorSets = 0;

            foreach ($match->sets as $set) {
                if ($set['local_points'] > $set['visitor_points']) {
                    $localSets++;
                } else {
                    $visitorSets++;
                }
            }

            // Determinar ganador
            $winnerTeamId = $localSets > $visitorSets ? $match->local_team_id : $match->visitor_team_id;
            $match->winner_team_id = $winnerTeamId;

            // Actualizar tabla de posiciones
            $this->updateLeaderboard($match->localTeam, $match->visitorTeam, $winnerTeamId);

            $match->state = 'Concluido';
            $match->save();
        } else {
            $match->state = $validated['state'];
            $match->save();
        }

        return response()->json(['message' => 'State updated', 'match' => $match], 200);
    }

    public function updateSets(Request $request, $id)
    {
        $match = Match::find($id);

        if (!$match) {
            return response()->json(['message' => 'Match not found'], 404);
        }

        // Verificar que el estado del partido sea 'En curso'
        if ($match->state !== 'En curso') {
            return response()->json(['message' => 'Sets can only be updated for matches in progress (state: En curso)'], 422);
        }

        $validated = $request->validate([
            'sets' => 'required|array',
            'sets.*.set' => 'required|integer|min:1',
            'sets.*.local_points' => 'required|integer|min:0',
            'sets.*.visitor_points' => 'required|integer|min:0',
        ]);

        $sets = $validated['sets'];
        $setCount = count($sets);

        // Validar que sean exactamente 3 o 5 sets
        if ($setCount !== 3 && $setCount !== 5) {
            return response()->json(['message' => 'A match must have exactly 3 or 5 sets'], 422);
        }

        foreach ($sets as $index => $set) {
            $maxPoints = ($setCount === 5 && $set['set'] === 5) ? 15 : 25; // 5º set: 15 puntos, los demás: 25 puntos

            // Validar puntos máximos
            if ($set['local_points'] > $maxPoints || $set['visitor_points'] > $maxPoints) {
                return response()->json([
                    'message' => "Set {$set['set']}: Points exceed {$maxPoints}"
                ], 422);
            }

            // Validar diferencia mínima de 2 puntos
            if (abs($set['local_points'] - $set['visitor_points']) < 2) {
                return response()->json([
                    'message' => "Set {$set['set']}: The difference between points must be at least 2"
                ], 422);
            }
        }

        $match->update(['sets' => $sets]);

        return response()->json(['message' => 'Sets updated', 'match' => $match], 200);
    }

    //GENERACION DE ROL DE PARTIDOS
    public function generateSchedule(Request $request)
    {
        $validated = $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
            'start_date' => 'required|date',
            'interval_minutes' => 'required|integer|min:30',
        ]);

        $tournament = Tournament::find($validated['tournament_id']);
        $teams = Team::all();
        $fields = Field::all();

        if (count($teams) < 2) {
            return response()->json(['message' => 'At least two teams are required to generate matches'], 400);
        }

        $matches = [];
        $date = $validated['start_date'];
        $time = $tournament->allowed_hours_start;

        // Generar Round Robin
        $teamIds = $teams->pluck('id')->toArray();
        $rounds = count($teamIds) - 1; // Número de rondas para Round Robin
        $matchesPerRound = intdiv(count($teamIds), 2);

        // Algoritmo de Round Robin
        for ($round = 0; $round < $rounds; $round++) {
            for ($i = 0; $i < $matchesPerRound; $i++) {
                $localTeamIndex = $i;
                $visitorTeamIndex = count($teamIds) - 1 - $i;

                $localTeamId = $teamIds[$localTeamIndex];
                $visitorTeamId = $teamIds[$visitorTeamIndex];

                $field = $fields->random(); // Elegir una cancha aleatoria

                $matches[] = Match::create([
                    'local_team_id' => $localTeamId,
                    'visitor_team_id' => $visitorTeamId,
                    'field_id' => $field->id,
                    'date' => $date,
                    'time' => $time,
                    'tournament_id' => $tournament->id,
                ]);

                // Ajustar hora y fecha
                $time = date('H:i', strtotime("+{$validated['interval_minutes']} minutes", strtotime($time)));
                if ($time >= $tournament->allowed_hours_end) {
                    $time = $tournament->allowed_hours_start;
                    $date = date('Y-m-d', strtotime('+1 day', strtotime($date)));
                }
            }

            // Rotación de equipos (excepto el primero)
            $rotatedTeams = array_splice($teamIds, 1);
            array_push($rotatedTeams, array_shift($rotatedTeams));
            $teamIds = array_merge([$teamIds[0]], $rotatedTeams);
        }

        return response()->json(['message' => 'Schedule generated successfully', 'matches' => $matches], 201);
    }

    //Actualizar la tabla de posiciones
    private function updateLeaderboard($localTeam, $visitorTeam, $winnerTeamId)
    {
        // Actualizar estadísticas del equipo local
        $localTeam->increment('matches_played');
        if ($localTeam->id === $winnerTeamId) {
            $localTeam->increment('matches_won');
            $localTeam->increment('points', 3);
        } else {
            $localTeam->increment('matches_lost');
            $localTeam->increment('points', 1);
        }

        // Actualizar estadísticas del equipo visitante
        $visitorTeam->increment('matches_played');
        if ($visitorTeam->id === $winnerTeamId) {
            $visitorTeam->increment('matches_won');
            $visitorTeam->increment('points', 3);
        } else {
            $visitorTeam->increment('matches_lost');
            $visitorTeam->increment('points', 1);
        }
    }

    //Buscar partidos por ID de torneo
    public function getMatchesByTournament($id)
    {
        // Buscar los partidos relacionados con el ID del torneo
        $matches = Match::with(['localTeam', 'visitorTeam', 'field', 'tournament'])
            ->where('tournament_id', $id)
            ->get();

        return response()->json($matches, 200);
    }
}