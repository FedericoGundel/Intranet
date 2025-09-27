<?php

namespace App\Http\Controllers;

use App\Models\PrestamoFamiliar;
use Illuminate\Http\Request;

class PrestamoFamiliarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('leyma-creditos.prestamos-familiares.index');
    }

    /**
     * Obtener datos para DataTable
     */
    public function data(Request $request)
    {
        $query = PrestamoFamiliar::with('usuario');

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha_prestamo', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $prestamos = $query->orderBy('fecha_prestamo', 'desc')->get();

        $data = $prestamos->map(function ($prestamo) {
            return [
                'id' => $prestamo->id,
                'familiar_nombre' => $prestamo->familiar_nombre,
                'familiar_dni' => $prestamo->familiar_dni,
                'familiar_telefono' => $prestamo->familiar_telefono,
                'monto' => $prestamo->monto,
                'fecha_prestamo' => $prestamo->fecha_prestamo->format('d/m/Y'),
                'fecha_vencimiento' => $prestamo->fecha_vencimiento ? $prestamo->fecha_vencimiento->format('d/m/Y') : null,
                'estado' => $prestamo->estado,
                'monto_pagado' => $prestamo->monto_pagado,
                'saldo_pendiente' => $prestamo->saldo_pendiente,
                'esta_vencido' => $prestamo->esta_vencido,
                'observaciones' => $prestamo->observaciones,
                'usuario_nombre' => $prestamo->usuario->name,
                'created_at' => $prestamo->created_at->format('d/m/Y H:i')
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
            'familiar_nombre' => 'required|string|max:255',
            'familiar_dni' => 'nullable|string|max:20',
            'familiar_telefono' => 'nullable|string|max:20',
            'monto' => 'required|numeric|min:0.01',
            'fecha_prestamo' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_prestamo',
            'observaciones' => 'nullable|string'
        ]);

        $prestamo = PrestamoFamiliar::create([
            ...$validated,
            'estado' => 'activo',
            'monto_pagado' => 0.0,
            'usuario_id' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Préstamo familiar registrado correctamente',
            'prestamo' => $prestamo
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $prestamo = PrestamoFamiliar::with('usuario')->findOrFail($id);

        return response()->json($prestamo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $prestamo = PrestamoFamiliar::findOrFail($id);

        $validated = $request->validate([
            'familiar_nombre' => 'required|string|max:255',
            'familiar_dni' => 'nullable|string|max:20',
            'familiar_telefono' => 'nullable|string|max:20',
            'monto' => 'required|numeric|min:0.01',
            'fecha_prestamo' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_prestamo',
            'observaciones' => 'nullable|string'
        ]);

        $prestamo->update($validated);
        $prestamo->actualizarEstado();

        return response()->json([
            'success' => true,
            'message' => 'Préstamo familiar actualizado correctamente',
            'prestamo' => $prestamo
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $prestamo = PrestamoFamiliar::findOrFail($id);
        $prestamo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Préstamo familiar eliminado correctamente'
        ]);
    }

    /**
     * Obtener total por período
     */
    public function totalPeriodo(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->toDateString());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->toDateString());

        $total = PrestamoFamiliar::getTotalPorPeriodo($fechaInicio, $fechaFin);

        return response()->json(['total' => $total]);
    }

    /**
     * Obtener préstamos vencidos
     */
    public function vencidos()
    {
        $prestamos = PrestamoFamiliar::activos()
            ->where('fecha_vencimiento', '<', now())
            ->where('saldo_pendiente', '>', 0)
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        return response()->json($prestamos);
    }
}
