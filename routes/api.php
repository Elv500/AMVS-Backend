<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Ruta protegida con autenticación, no afecta las rutas de equipos
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Ruta para listar todos los equipos
Route::get('teams', [TeamController::class, 'index']);

// Ruta para mostrar un equipo específico por su ID
Route::get('teams/{id}', [TeamController::class, 'show']);

// Ruta para crear un nuevo equipo
Route::post('teams', [TeamController::class, 'store']);

// Ruta para actualizar un equipo existente
Route::put('teams/{id}', [TeamController::class, 'update']);

// Ruta para eliminar un equipo
Route::delete('teams/{id}', [TeamController::class, 'destroy']);