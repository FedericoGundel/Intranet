<?php

namespace App\Http\Controllers;

use App\Models\Inyeccion;
use Illuminate\Http\Request;

class InyeccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('leyma-creditos.inyecciones.index');
    }

    /**
     * Obtener datos para DataTable
     */
    public function data(Request $request)
    {
        $query = Inyeccion::with('usuario');

        // Filtros
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $inyecciones = $query->orderBy('fecha', 'desc')->get();

        $data = $inyecciones->map(function ($inyeccion) {
            return [
                'id' => $inyeccion->id,
                'monto' => $inyeccion->monto,
                'concepto' => $inyeccion->concepto,
                'fecha' => $inyeccion->fecha->format('d/m/Y'),
                'observaciones' => $inyeccion->observaciones,
                'usuario_nombre' => $inyeccion->usuario->name,
                'created_at' => $inyeccion->created_at->format('d/m/Y H:i')
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'concepto' => 'required|string|max:255',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string'
        ]);

        $inyeccion = Inyeccion::create([
            ...$validated,
            'usuario_id' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inyección registrada correctamente',
            'inyeccion' => $inyeccion
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $inyeccion = Inyeccion::with('usuario')->findOrFail($id);

        return response()->json($inyeccion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $inyeccion = Inyeccion::findOrFail($id);

        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'concepto' => 'required|string|max:255',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string'
        ]);

        $inyeccion->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Inyección actualizada correctamente',
            'inyeccion' => $inyeccion
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $inyeccion = Inyeccion::findOrFail($id);
        $inyeccion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inyección eliminada correctamente'
        ]);
    }

    /**
     * Obtener total por período
     */
    public function totalPeriodo(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->toDateString());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->toDateString());

        $total = Inyeccion::getTotalPorPeriodo($fechaInicio, $fechaFin);

        return response()->json(['total' => $total]);
    }
}
