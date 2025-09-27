<?php

namespace App\Http\Controllers;

use App\Models\GastoLeyma;
use Illuminate\Http\Request;

class GastoLeymaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('leyma-creditos.gastos.index');
    }

    /**
     * Obtener datos para DataTable
     */
    public function data(Request $request)
    {
        $query = GastoLeyma::with('usuario');

        // Filtros
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $gastos = $query->orderBy('fecha', 'desc')->get();

        $data = $gastos->map(function ($gasto) {
            return [
                'id' => $gasto->id,
                'categoria' => $gasto->categoria,
                'categoria_label' => ucfirst($gasto->categoria),
                'monto' => $gasto->monto,
                'concepto' => $gasto->concepto,
                'fecha' => $gasto->fecha->format('d/m/Y'),
                'observaciones' => $gasto->observaciones,
                'usuario_nombre' => $gasto->usuario->name,
                'created_at' => $gasto->created_at->format('d/m/Y H:i')
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
            'categoria' => 'required|in:mauro,gota,inversion',
            'monto' => 'required|numeric|min:0.01',
            'concepto' => 'required|string|max:255',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string'
        ]);

        $gasto = GastoLeyma::create([
            ...$validated,
            'usuario_id' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gasto registrado correctamente',
            'gasto' => $gasto
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $gasto = GastoLeyma::with('usuario')->findOrFail($id);

        return response()->json($gasto);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $gasto = GastoLeyma::findOrFail($id);

        $validated = $request->validate([
            'categoria' => 'required|in:mauro,gota,inversion',
            'monto' => 'required|numeric|min:0.01',
            'concepto' => 'required|string|max:255',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string'
        ]);

        $gasto->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Gasto actualizado correctamente',
            'gasto' => $gasto
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $gasto = GastoLeyma::findOrFail($id);
        $gasto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gasto eliminado correctamente'
        ]);
    }

    /**
     * Obtener resumen por categorías
     */
    public function resumenCategorias(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->toDateString());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->toDateString());

        $resumen = GastoLeyma::getResumenPorCategoria($fechaInicio, $fechaFin);

        return response()->json($resumen);
    }

    /**
     * Obtener total por período
     */
    public function totalPeriodo(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->toDateString());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->toDateString());

        $total = GastoLeyma::getTotalPorPeriodo($fechaInicio, $fechaFin);

        return response()->json(['total' => $total]);
    }
}
