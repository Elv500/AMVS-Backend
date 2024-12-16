<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::orderBy('created_at', 'desc')->get(); // Listar noticias más recientes primero
        return response()->json($news, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Archivo opcional (máx. 2MB)
        ]);

        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('public/news');
            $validated['attachment'] = str_replace('public/', 'storage/', $filePath); // Convertir a ruta pública
        }

        $news = News::create($validated);
        return response()->json($news, 201);
    }

    public function show($id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json(['message' => 'News not found'], 404);
        }

        return response()->json($news, 200);
    }

    public function update(Request $request, $id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json(['message' => 'News not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Archivo opcional
        ]);

        if ($request->hasFile('attachment')) {
            // Eliminar el archivo anterior si existe
            if ($news->attachment) {
                Storage::delete(str_replace('storage/', 'public/', $news->attachment));
            }

            $filePath = $request->file('attachment')->store('public/news');
            $validated['attachment'] = str_replace('public/', 'storage/', $filePath);
        }

        $news->update($validated);
        return response()->json($news, 200);
    }

    public function destroy($id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json(['message' => 'News not found'], 404);
        }

        // Eliminar el archivo adjunto si existe
        if ($news->attachment) {
            Storage::delete(str_replace('storage/', 'public/', $news->attachment));
        }

        $news->delete();
        return response()->json(['message' => 'News deleted'], 200);
    }
}