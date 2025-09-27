<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GastoController extends Controller
{
    /**
     * Listar todos los gastos (JSON).
     */

    public function index()
    {
        return view('gastos'); // tu vista con la tabla
    }

    public function data(Request $request)
    {
        // Eager-load para evitar N+1
        $gastos = Gasto::with([
            'empleado:id,nombre,apellido',
            'pagoNomina:id,nomina_id,importe,fecha', // por si querés mostrar algo del pago
        ])
            ->orderByDesc('fecha')
            ->get();

        $rows = $gastos->map(function ($g) {
            $empleadoNombre = trim(($g->empleado->nombre ?? '') . ' ' . ($g->empleado->apellido ?? ''));

            return [
                'id'              => $g->id,
                'descripcion'     => $g->descripcion,
                'fecha'           => $g->fecha, // o (string) $g->fecha
                'monto'           => (float) $g->monto,
                'categoria'       => $g->categoria,
                'subcategoria'    => $g->subcategoria,
                'metodo_pago'     => $g->metodo_pago,
                'empleado'        => $empleadoNombre ?: null,
                'empleado_id'     => $g->empleado_id,
                'nomina_pago_id'  => $g->nomina_pago_id, // clave para mostrar "Pago Nómina #..."
                // URL pública al comprobante si existe
                'comprobante_url' => $g->comprobante_path ? Storage::url($g->comprobante_path) : null,
                // opcional: “origen” para lógica en el front
                'origen'          => $g->nomina_pago_id ? 'pago_nomina' : 'manual',
                'created_at'      => optional($g->created_at)->toDateTimeString(),
                'updated_at'      => optional($g->updated_at)->toDateTimeString(),
            ];
        });

        return response()->json(['data' => $rows]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha'        => 'required|date',
            'categoria'    => 'required|string|max:100',
            'subcategoria' => 'nullable|string|max:100',
            'monto'        => 'required|numeric|min:0',
            'metodo_pago'  => 'required|string|max:50',
            'descripcion'  => 'nullable|string',
            'empleado_id'  => 'nullable|exists:empleados,id',
            'nomina_pago_id' => 'nullable|exists:pago_nominas,id',
            'comprobante'  => 'nullable|file|mimes:pdf,jpg,png',
        ]);

        if ($request->hasFile('comprobante')) {
            $validated['comprobante_path'] = $request->file('comprobante')->store('comprobantes', 'public');
        }

        $gasto = Gasto::create($validated);

        return response()->json($gasto, 201);
    }

    public function show($id)
    {
        $gasto = Gasto::with(['empleado', 'pagoNomina'])->findOrFail($id);
        return response()->json($gasto);
    }

    public function update(Request $request, $id)
    {
        $gasto = Gasto::findOrFail($id);

        $validated = $request->validate([
            'fecha'        => 'required|date',
            'categoria'    => 'required|string|max:100',
            'subcategoria' => 'nullable|string|max:100',
            'monto'        => 'required|numeric|min:0',
            'metodo_pago'  => 'required|string|max:50',
            'descripcion'  => 'nullable|string',
            'empleado_id'  => 'nullable|exists:empleados,id',
            'nomina_pago_id' => 'nullable|exists:pago_nominas,id',
            'comprobante'  => 'nullable|file|mimes:pdf,jpg,png',
        ]);

        if ($request->hasFile('comprobante')) {
            if ($gasto->comprobante_path) {
                Storage::disk('public')->delete($gasto->comprobante_path);
            }
            $validated['comprobante_path'] = $request->file('comprobante')->store('comprobantes', 'public');
        }

        $gasto->update($validated);

        return response()->json($gasto);
    }

    public function destroy($id)
    {
        $gasto = Gasto::findOrFail($id);

        if ($gasto->comprobante_path) {
            Storage::disk('public')->delete($gasto->comprobante_path);
        }

        $gasto->delete();

        return response()->json(['message' => 'Gasto eliminado correctamente']);
    }
}