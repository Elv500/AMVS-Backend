<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\TournamentController;

// Ruta protegida con autenticación, no afecta las rutas de equipos
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::apiResource('teams', TeamController::class);
Route::apiResource('coaches', CoachController::class);


//Rutas para los jugadores
Route::get('players', [PlayerController::class, 'index']);
Route::post('players', [PlayerController::class, 'store']);
Route::get('players/{id}', [PlayerController::class, 'show']);
Route::put('players/{id}', [PlayerController::class, 'update']);
Route::delete('players/{id}', [PlayerController::class, 'destroy']);

//Rutas para las canchas
Route::get('fields', [FieldController::class, 'index']);
Route::post('fields', [FieldController::class, 'store']);
Route::get('fields/{id}', [FieldController::class, 'show']);
Route::put('fields/{id}', [FieldController::class, 'update']);
Route::delete('fields/{id}', [FieldController::class, 'destroy']);

//Rutas para los partidos
Route::get('matches', [MatchController::class, 'index']);
Route::post('matches', [MatchController::class, 'store']);
Route::get('matches/{id}', [MatchController::class, 'show']);
Route::put('matches/{id}', [MatchController::class, 'update']);
Route::delete('matches/{id}', [MatchController::class, 'destroy']);

//Rutas estado de los partidos
Route::put('matches/{id}/state', [MatchController::class, 'updateState']);
Route::put('matches/{id}/sets', [MatchController::class, 'updateSets']);

//Ruta del rol de partidos
Route::post('matches/generate', [MatchController::class, 'generateSchedule']);

//Ruta de torneos
Route::apiResource('tournaments', TournamentController::class);