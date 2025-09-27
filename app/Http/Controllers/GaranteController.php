<?php

namespace App\Http\Controllers;

use App\Models\Garante;
use Illuminate\Http\Request;

class GaranteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('garantes.index');
    }

    /**
     * Obtener datos para DataTable
     */
    public function data()
    {
        $garantes = Garante::with('cliente')->get();

        $data = $garantes->map(function ($garante) {
            return [
                'id' => $garante->id,
                'nombre' => $garante->nombre,
                'dni' => $garante->dni,
                'telefono' => $garante->telefono,
                'domicilio' => $garante->domicilio,
                'cliente_nombre' => $garante->cliente ? $garante->cliente->nombre : 'Sin cliente',
                'created_at' => $garante->created_at->format('d/m/Y')
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:garantes,dni',
            'telefono' => 'required|string|max:20',
            'domicilio' => 'required|string',
            'cliente_id' => 'required|exists:clientes,id'
        ]);

        $garante = Garante::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Garante creado correctamente',
            'garante' => $garante
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Garante $garante)
    {
        return response()->json($garante->load('cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Garante $garante)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Garante $garante)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:garantes,dni,' . $garante->id,
            'telefono' => 'required|string|max:20',
            'domicilio' => 'required|string',
            'cliente_id' => 'required|exists:clientes,id'
        ]);

        $garante->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Garante actualizado correctamente',
            'garante' => $garante
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Garante $garante)
    {
        $garante->delete();

        return response()->json([
            'success' => true,
            'message' => 'Garante eliminado correctamente'
        ]);
    }
}
