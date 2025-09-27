<?php

namespace App\Services;

use App\Models\Factura;
use App\Models\Articulo;
use App\Models\ArticuloFactura;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\PagoService;

class FacturaService
{
    protected PagoService $pagoService;

    public function __construct(PagoService $pagoService)
    {
        $this->pagoService = $pagoService;
    }
    public function crearFactura(array $payload, ?callable $afterCommit = null): Factura
    {
        return DB::transaction(function () use ($payload, $afterCommit) {
            $dataFactura = $payload['factura'] ?? [];
            $items       = $payload['articulos'] ?? [];

            if (!is_array($items) || count($items) === 0) {
                throw ValidationException::withMessages(['articulos' => 'Debe incluir al menos un artículo.']);
            }

            // Normalizar/calcular totales por línea (desc -> imp)
            $items = array_map(function ($a) {
                $a['id_articulo'] = $a['id_articulo'] ?? null;
                $a['cantidad']    = (int)   ($a['cantidad'] ?? 0);
                $a['precio']      = (float) ($a['precio']   ?? 0);

                $a['descuento']   = (float) ($a['descuento'] ?? 0);
                if ($a['descuento'] < 0)   $a['descuento'] = 0;
                if ($a['descuento'] > 100) $a['descuento'] = 100;

                $a['impuesto']    = (float) ($a['impuesto']  ?? 0);
                if ($a['impuesto'] < 0)     $a['impuesto'] = 0;

                $precioConDesc    = $a['precio'] * (1 - $a['descuento'] / 100);
                $subtotal         = $precioConDesc * $a['cantidad'];
                $impuestoMonto    = $subtotal * ($a['impuesto'] / 100);
                $a['total']       = round($subtotal + $impuestoMonto, 2);

                return $a;
            }, $items);

            // Chequeo de stock acumulado (con lock)
            $cantPorArticulo = collect($items)
                ->filter(fn($a) => !empty($a['id_articulo']))
                ->groupBy('id_articulo')
                ->map->sum('cantidad');

            foreach ($cantPorArticulo as $artId => $cant) {
                $art = Articulo::whereKey($artId)->lockForUpdate()->first();
                if (!$art || $art->stock < $cant) {
                    throw ValidationException::withMessages([
                        'stock' => "Stock insuficiente para el artículo ID {$artId}."
                    ]);
                }
            }

            // Crear factura
            /** @var Factura $factura */
            $factura = Factura::create($dataFactura);

            // Crear líneas (Observer moverá stock)
            foreach ($items as $a) {
                ArticuloFactura::create([
                    'factura_id'  => $factura->id,
                    'id_articulo' => $a['id_articulo'],
                    'nombre'      => $a['nombre'],
                    'cantidad'    => $a['cantidad'],
                    'precio'      => $a['precio'],
                    'descuento'   => $a['descuento'],
                    'impuesto'    => $a['impuesto'],
                    'descripcion' => $a['descripcion'] ?? null,
                    'total'       => $a['total'],
                ]);
            }

            // (Opcional) Actualizar columna denormalizada 'total' de la factura
            // $factura->update(['total' => array_sum(array_column($items, 'total'))]);
            $totalFactura = array_sum(array_column($items, 'total'));

            $senar = (bool)($dataFactura['senar'] ?? false);
            $porc  = (float)($dataFactura['sena_factura_porcentaje'] ?? 0);

            if ($senar && $porc > 0) {
                $montoSena = round($totalFactura * ($porc / 100), 2);

                // armamos el payload del pago
                $pagoData = [
                    'factura_id' => $factura->id,
                    'fecha'      => $dataFactura['fecha'] ?? now()->toDateString(),
                    'monto'      => $montoSena,
                    'metodo'     => 'Seña',
                    'referencia' => "Seña {$porc}%",
                    'notas'      => 'Generado automáticamente al crear factura con seña',
                ];

                // Registrar pago dentro de la misma TX
                $this->pagoService->registrarPago($pagoData);
            }

            if ($afterCommit) {
                DB::afterCommit(function () use ($afterCommit, $factura) {
                    $afterCommit($factura);
                });
            }

            return $factura;
        });
    }

    public function eliminarFactura(Factura $factura): void
    {
        DB::transaction(function () use ($factura) {
            foreach ($factura->articuloFacturas as $item) {
                $item->delete(); // Observer sumará stock
            }
            $factura->delete();
        });
    }

    /* =========================
     * CONSULTAS REUSABLES
     * ========================= */

    /** Query base más frecuente (cliente básico) */
    private function baseQuery(): Builder
    {
        return Factura::query()
            ->with(['cliente:id,nombre']);
    }

    /** Suma total de ítems en DB usando la columna 'total' de articulo_facturas */
    private function withTotalFacturado(Builder $q): Builder
    {
        // Si NO tuvieras la columna 'total' fiable, usarías una subconsulta calculada.
        return $q->withSum('articuloFacturas as total_facturado', 'total');
    }

    /** Suma de pagos en DB */
    private function withTotalPagado(Builder $q): Builder
    {
        return $q->withSum('pagos as total_pagado', 'monto');
    }

    /* =========================
     * VISTAS / LISTADOS
     * ========================= */

    /** Para la tabla de Facturas (resumen) */
    public function listarFacturasResumen()
    {
        $q = $this->withTotalFacturado($this->baseQuery())
            ->orderByDesc('fecha');

        return $q->get()->map(function ($f) {
            $total = (float) ($f->total_facturado ?? 0);

            return [
                'id'                => $f->id,
                'nombre'            => $f->nombre ?? ($f->cliente->nombre ?? 'N/A'),
                'numero'            => $f->numero,
                'fecha'             => (string) $f->fecha,
                'fecha_vencimiento' => $f->fecha_vencimiento,
                'condiciones'       => $f->condiciones,
                'total'             => round($total, 2),
            ];
        });
    }
    public function listarFacturasCobrosPorVendedor(int $empleadoId)
    {
        $q = $this->withTotalPagado(
            $this->withTotalFacturado(
                $this->baseQuery()->where('vendedor_id', $empleadoId)
            )
        )->orderByDesc('fecha');

        return $q->get()->map(function ($f) {
            $total  = (float) ($f->total_facturado ?? 0);
            $pagado = (float) ($f->total_pagado ?? 0);
            $saldo  = max(0, $total - $pagado);

            return [
                'id'      => $f->id,
                'numero'  => $f->numero,
                'fecha'   => (string) $f->fecha,
                'cliente' => $f->nombre ?? ($f->cliente->nombre ?? 'N/A'),
                'total'   => round($total, 2),
                'pagado'  => round($pagado, 2),
                'saldo'   => round($saldo, 2),
                'estado'  => $saldo <= 0 ? 'pagada' : ($pagado > 0 ? 'parcial' : 'emitida'),
            ];
        });
    }
    /** Para la tabla de Cobros (total, pagado, saldo, estado) */
    public function listarFacturasCobros()
    {
        $q = $this->withTotalPagado(
            $this->withTotalFacturado($this->baseQuery())
        )->orderByDesc('fecha');

        return $q->get()->map(function ($f) {
            $total  = (float) ($f->total_facturado ?? 0);
            $pagado = (float) ($f->total_pagado ?? 0);
            $saldo  = max(0, $total - $pagado);

            return [
                'id'      => $f->id,
                'numero'  => $f->numero,
                'fecha'   => (string) $f->fecha,
                'cliente' => $f->nombre ?? ($f->cliente->nombre ?? 'N/A'),
                'total'   => round($total, 2),
                'pagado'  => round($pagado, 2),
                'saldo'   => round($saldo, 2),
                'estado'  => $saldo <= 0 ? 'pagada' : ($pagado > 0 ? 'parcial' : 'emitida'),
            ];
        });
    }

    /** Detalle completo para ver/pdf */
    public function obtenerDetalle(int $id): Factura
    {
        return Factura::with([
            'cliente',
            'articuloFacturas.articulo',
            'pagos',
        ])->findOrFail($id);
    }
}