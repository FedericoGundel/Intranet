<?php

namespace App\Http\Controllers;

use App\Models\PlantillaEmail;
use Illuminate\Http\Request;

class PlantillaEmailController extends Controller
{
    public function index()
    {
        $plantillas = PlantillaEmail::all();
        return view('plantilla_emails.index', compact('plantillas'));
    }

    public function create()
    {
        return view('plantilla_emails.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'asunto' => 'required|string|max:255',
            'cuerpo' => 'required|string',
        ]);

        $plantilla = PlantillaEmail::create($validated);

        return response()->json([
            'message' => 'Plantilla creada correctamente',
            'plantilla' => $plantilla
        ]);
    }
    public function show($id)
    {
        $cliente = PlantillaEmail::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Plantilla no encontrada'], 404);
        }

        return response()->json($cliente);
    }
    public function dataTom(Request $request)
    {


        $query = PlantillaEmail::query();


        // Si no hay $q, esto devuelve todos los clientes (o límite para no saturar)
        $clientes = $query->get();

        return response()->json(
            $clientes->map(function ($cliente) {
                return [
                    'value' => $cliente->id,
                    'text'  => "{$cliente->nombre}",
                ];
            })
        );
    }

    public function update(Request $request, $id)
    {
        $plantilla = PlantillaEmail::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'asunto' => 'required|string|max:255',
            'cuerpo' => 'nullable|string',
        ]);

        $plantilla->update($validated);

        return response()->json(['message' => 'Plantilla actualizada correctamente']);
    }
    public function destroy($id)
    {
        $plantilla = PlantillaEmail::find($id);

        if (!$plantilla) {
            return response()->json(['message' => 'Plantilla no encontrada'], 404);
        }

        $plantilla->delete();

        return response()->json(['message' => 'Plantilla eliminada correctamente']);
    }
}
