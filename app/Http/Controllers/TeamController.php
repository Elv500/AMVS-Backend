<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{

    public function index()
    {
        //Retorna todos los equipos de la tabla 'teams'
        return Team::all();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'name' => 'required|string|max:10',
            'coach' => 'nullable|string|max:10',
        ]);

        //Crear el equipo y guardarlo en la base de datos
        $team = Team::create($request->all());

        //Retorna el equipo creado con un codigo de respuesta 201(creado)
        return response()->json($team,201);
    }

    public function show($id)
    {
        //Encuentra el equipo por su ID o lanza un error si no existe
        return Team::findOrFail($id);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //Validar los datos de entrada
        $request->validate([
            'name' => 'required|string|max:10',
            'coach' => 'nullable|string|max:10'
        ]);

        //Encuentra el equipo por su ID o lanza un error 404 si no existe
        $team = Team::findOrFail($id);

        //Actualizar los datos del equipo
        $team->update($request->all());

        //Retorna el equipo actualizado
        return response()->json($team);
    }

    public function destroy($id)
    {
        //Encuentra el equipo por su ID o lanza un error 404 si no existe
        $team = Team::findOrFail($id);

        //Eliminar el equipo
        $team->delete();

        //Retornar una respuesta de éxito sin contenido
        return response()->json(null,204);
    }
}
