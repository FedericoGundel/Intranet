<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Pago;
use App\Services\PagoService;
use Illuminate\Http\Request;

class PagosController extends Controller
{
    public function __construct(
        private PagoService $service
    ) {}

    /**
     * GET /pagos?factura_id=OPCIONAL
     * GET /facturas/{factura}/pagos
     */
    public function index(Request $r, ?int $factura = null)
    {
        // factura puede venir por ruta (/facturas/{factura}/pagos) o por query (?factura_id=)
        $facturaId = $factura ?? $r->integer('factura_id');
        $perPage = (int) $r->input('per_page', 25);
        $paginate = $r->boolean('paginate', true);

        $query = Pago::query()
            ->when($facturaId, fn($q) => $q->where('factura_id', $facturaId))
            ->with(['factura' => function ($q) {
                // Traemos datos mínimos de factura + agregados calculados en DB
                $q->select('id', 'numero', 'fecha');  // agregá otros campos si querés
                $q
                    ->withSum('articuloFacturas as total_facturado', 'total')
                    ->withSum('pagos as total_pagado', 'monto');
            }])
            ->orderByDesc('fecha')
            ->orderByDesc('id');

        $transform = function (Pago $p) {
            $total = (float) ($p->factura->total_facturado ?? 0);
            $pagado = (float) ($p->factura->total_pagado ?? 0);
            $saldo = max(0, $total - $pagado);

            return [
                'id' => $p->id,
                'numero' => $p->numero,
                'fecha' => (string) $p->fecha,
                'monto' => (float) $p->monto,
                'metodo' => $p->metodo,
                'referencia' => $p->referencia,
                'notas' => $p->notas,
                'factura' => [
                    'id' => $p->factura->id,
                    'numero' => $p->factura->numero,
                    'fecha' => (string) $p->factura->fecha,
                    'total' => round($total, 2),
                    'pagado' => round($pagado, 2),
                    'saldo' => round($saldo, 2),
                    'estado' => $saldo <= 0 ? 'pagada' : ($pagado > 0 ? 'parcial' : 'emitida'),
                ],
            ];
        };

        if ($paginate) {
            // Paginado (devuelve meta de paginación de Laravel)
            $pagos = $query->paginate($perPage)->through($transform);
            return response()->json($pagos);
        } else {
            // Lista completa
            $pagos = $query->get()->map($transform);
            return response()->json(['data' => $pagos]);
        }
    }

    public function show($id)
    {
        $pago = \App\Models\Pago::findOrFail($id);
        return response()->json($pago);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'factura_id' => 'required|exists:facturas,id',
            'fecha' => 'required|date',
            'monto' => 'required',
            'metodo' => 'nullable|string|max:100',
            'referencia' => 'nullable|string|max:150',
            'notas' => 'nullable|string',
            'cobrador_id' => 'nullable|exists:cobradores,empleado_id',  // 👈 agregado
        ]);

        $pago = $this->service->registrarPago($data);
        $factura = Factura::with('pagos')->find($data['factura_id']);

        return response()->json([
            'success' => true,
            'message' => 'Pago registrado',
            'pago' => $pago,
            'factura' => $factura,
        ]);
    }

    public function update(Request $r, $id)
    {
        $data = $r->validate([
            'fecha' => 'required|date',
            'monto' => 'required',  // el Service normaliza/valida
            'metodo' => 'nullable|string|max:100',
            'referencia' => 'nullable|string|max:150',
            'notas' => 'nullable|string',
            'factura_id' => 'nullable|exists:facturas,id',
            'cobrador_id' => 'nullable|exists:cobradores,empleado_id',  // 👈 agregado
        ]);

        $pago = $this->service->actualizarPago((int) $id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Pago actualizado',
            'pago' => $pago,
        ]);
    }

    public function destroy(Request $r, $id)
    {
        $force = $r->boolean('force', false);

        $this->service->eliminarPagoPorId((int) $id, $force);

        return response()->json([
            'success' => true,
            'message' => $force ? 'Pago eliminado definitivamente' : 'Pago anulado',
        ]);
    }

    public function restore($id)
    {
        $this->service->restaurarPagoPorId((int) $id);

        return response()->json([
            'success' => true,
            'message' => 'Pago restaurado',
        ]);
    }
}
