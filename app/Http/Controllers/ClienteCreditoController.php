<?php

namespace App\Http\Controllers;

use App\Models\ClienteCredito;
use Illuminate\Http\Request;

class ClienteCreditoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('leyma-creditos.clientes.index');
    }

    /**
     * Obtener datos para DataTable
     */
    public function data()
    {
        $clientes = ClienteCredito::with(['creditos' => function ($query) {
            $query->select('id', 'cliente_id', 'monto_principal', 'monto_total', 'fecha_vencimiento', 'tipo_pago', 'cantidad_cuotas');
        }])->get();

        $data = $clientes->map(function ($cliente) {
            $creditosActivos = $cliente->creditos->filter(function ($credito) {
                return $credito->estado === 'activo';
            });
            $totalPrestado = $cliente->creditos->sum('monto_principal');
            $totalPagado = $cliente->creditos->sum(function ($credito) {
                return $credito->monto_pagado;
            });

            return [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre ?? 'Sin nombre',
                'dni' => $cliente->dni ?? 'N/A',
                'telefono' => $cliente->telefono ?? 'N/A',
                'domicilio' => $cliente->domicilio ?? 'N/A',
                'comercio_negocio' => $cliente->comercio_negocio ?? 'N/A',
                'estado' => $cliente->estado ?? 'inactivo',
                'tiene_garante' => $cliente->tiene_garante ?? false,
                'garante_nombre' => $cliente->garante_nombre ?? 'Sin garante',
                'creditos_activos' => $creditosActivos->count(),
                'total_prestado' => $totalPrestado,
                'total_pagado' => $totalPagado,
                'saldo_total' => $totalPrestado - $totalPagado,
                'created_at' => $cliente->created_at ? $cliente->created_at->format('d/m/Y') : 'N/A'
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Obtener datos para select (TomSelect)
     */
    public function dataTom(Request $request)
    {
        $q = $request->input('q');

        $query = ClienteCredito::activos();

        if ($q) {
            $query->where(function ($query) use ($q) {
                $query
                    ->where('nombre', 'like', "%{$q}%")
                    ->orWhere('dni', 'like', "%{$q}%")
                    ->orWhere('comercio_negocio', 'like', "%{$q}%");
            });
        }

        $clientes = $query->orderBy('nombre')->get();

        return response()->json(
            $clientes->map(function ($cliente) {
                return [
                    'value' => $cliente->id,
                    'text' => "{$cliente->nombre} - {$cliente->dni}",
                    'dni' => $cliente->dni,
                    'telefono' => $cliente->telefono,
                    'comercio' => $cliente->comercio_negocio
                ];
            })
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:clientes_credito,dni',
            'domicilio' => 'required|string',
            'comercio_negocio' => 'nullable|string|max:255',
            'telefono' => 'required|string|max:20',
            'garante_nombre' => 'nullable|string|max:255',
            'garante_dni' => 'nullable|string|max:20',
            'garante_telefono' => 'nullable|string|max:20',
            'garante_domicilio' => 'nullable|string',
            'observaciones' => 'nullable|string'
        ]);

        $cliente = ClienteCredito::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cliente creado correctamente',
            'cliente' => $cliente
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cliente = ClienteCredito::with(['creditos.pagos', 'creditos.recargos', 'creditos.descuentos', 'saldosFavor'])
            ->findOrFail($id);

        return response()->json($cliente);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $cliente = ClienteCredito::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:clientes_credito,dni,' . $cliente->id,
            'domicilio' => 'required|string',
            'comercio_negocio' => 'nullable|string|max:255',
            'telefono' => 'required|string|max:20',
            'garante_nombre' => 'nullable|string|max:255',
            'garante_dni' => 'nullable|string|max:20',
            'garante_telefono' => 'nullable|string|max:20',
            'garante_domicilio' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo',
            'observaciones' => 'nullable|string'
        ]);

        $cliente->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cliente actualizado correctamente',
            'cliente' => $cliente
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cliente = ClienteCredito::findOrFail($id);

        // Verificar si tiene créditos activos
        if ($cliente->tieneCreditoActivo()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar un cliente con créditos activos'
            ], 422);
        }

        $cliente->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cliente eliminado correctamente'
        ]);
    }

    /**
     * Obtener historial de créditos del cliente
     */
    public function historialCreditos($id)
    {
        $cliente = ClienteCredito::findOrFail($id);

        $creditos = $cliente
            ->creditos()
            ->with(['pagos', 'recargos', 'descuentos'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($creditos);
    }

    /**
     * Obtener saldos a favor del cliente
     */
    public function saldosFavor($id)
    {
        $cliente = ClienteCredito::findOrFail($id);

        $saldos = $cliente
            ->saldosFavor()
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json($saldos);
    }

    /**
     * Obtener estadísticas de clientes
     */
    public function estadisticas()
    {
        $estadisticas = [
            'clientes_totales' => ClienteCredito::count(),
            'clientes_activos' => ClienteCredito::activos()->count(),
            'clientes_con_garante' => ClienteCredito::whereNotNull('garante_nombre')
                ->where('garante_nombre', '!=', '')
                ->count(),
            'clientes_con_creditos' => ClienteCredito::whereHas('creditos')->count(),
        ];

        return response()->json($estadisticas);
    }
}
