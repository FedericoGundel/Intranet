<?php

namespace App\Services;

use App\Models\Empleado;
use App\Models\Factura;
use App\Models\Gasto;
use App\Models\Nomina;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class NominaService
{
    /**
     * Listado genérico de nóminas para DataTables/JSON.
     * Filtros opcionales: empleado_id, estado, desde, hasta (YYYY-MM-DD)
     */
    public function eliminarNomina(int $id, bool $definitivo = false): void
    {
        DB::transaction(function () use ($id, $definitivo) {
            $nomina = Nomina::findOrFail($id);

            if ($definitivo) {
                $nomina->forceDelete();  // Borrado real
            } else {
                // SoftDelete (si el modelo tiene SoftDeletes)
                if (is_null($nomina->deleted_at)) {
                    $nomina->delete();
                }
            }
        });
    }

    public function listarNominas(Request $request)
    {
        $q = Nomina::query()
            ->with(['empleado', 'pagos', 'detalles'])
            ->orderByDesc('desde');

        if ($request->filled('empleado_id')) {
            $q->where('empleado_id', (int) $request->empleado_id);
        }

        if ($request->filled('estado')) {
            $q->where('estado', $request->estado);
        }

        if ($request->filled('desde') && $request->filled('hasta')) {
            $q->whereDate('desde', '>=', $request->desde)->whereDate('hasta', '<=', $request->hasta);
        }

        return $q->get()->map(fn($n) => $this->mapNominaFila($n));
    }

    /**
     * Listar nóminas por empleado (para modal por empleado).
     */
    public function listarNominasPorEmpleado(int $empleadoId)
    {
        // 404 si no existe
        Empleado::findOrFail($empleadoId);

        return Nomina::query()
            ->where('empleado_id', $empleadoId)
            ->orderByDesc('desde')
            ->get(['*'])
            ->map(fn($n) => $this->mapNominaFila($n));
    }

    /**
     * Preview de cálculo (no crea la nómina).
     * Valida que NO haya solapes en el rango.
     */

    /**
     * Crear o actualizar (upsert) una nómina en borrador para un empleado + rango.
     * Permite reusar la MISMA nómina (mismo rango exacto) pero bloquea solapes con otras.
     */
    public function upsert(int $empleadoId, string $desde, string $hasta, ?string $periodo = null, float $ajustes = 0): Nomina
    {
        $empleado = Empleado::with(['vendedor', 'cobrador'])->findOrFail($empleadoId);

        if (!$empleado->vendedor && !$empleado->cobrador) {
            abort(422, 'El empleado no es vendedor ni cobrador.');
        }

        // Determinar rol y porcentaje
        if ($empleado->vendedor) {
            $rol = 'vendedor';
            $porc = (float) ($empleado->vendedor->comision_porcentaje ?? 9.0);
        } else {
            $rol = 'cobrador';
            $porc = (float) ($empleado->cobrador->comision_porcentaje ?? 3.0);
        }

        $d = Carbon::parse($desde)->startOfDay();
        $h = Carbon::parse($hasta)->endOfDay();
        $dStr = $d->toDateString();
        $hStr = $h->toDateString();

        // 1. Verificar si existe exacta
        $existeExacta = Nomina::where('empleado_id', $empleado->id)
            ->whereDate('desde', $dStr)
            ->whereDate('hasta', $hStr)
            ->exists();

        if ($existeExacta) {
            abort(422, 'Ya existe una nómina exacta para este rango de fechas. Debe eliminarla antes de generar otra.');
        }

        // 2. Verificar solapamiento
        if ($this->existeSolape($empleado->id, $dStr, $hStr)) {
            abort(422, 'Ya existe una nómina que solapa con ese rango de fechas para este empleado.');
        }

        // 3. Crear nueva nómina
        $nomina = Nomina::create([
            'empleado_id' => $empleado->id,
            'desde' => $dStr,
            'hasta' => $hStr,
        ]);

        $totalBase = 0.0;
        $totalComision = 0.0;
        $lineas = [];

        if ($rol === 'vendedor') {
            // Totales desde Facturas
            $facturas = Factura::query()
                ->where('vendedor_id', $empleado->id)
                ->whereBetween('fecha', [$d, $h])
                ->when(Schema::hasColumn('facturas', 'estado'), fn($q) => $q->whereNotIn('estado', ['anulada', 'nota_credito']))
                ->withSum('articuloFacturas as total_facturado', 'total')
                ->get(['id', 'fecha']);

            foreach ($facturas as $f) {
                $base = (float) ($f->total_facturado ?? 0);
                if ($base <= 0)
                    continue;

                $comi = round(($base * $porc) / 100, 2);
                $totalBase += $base;
                $totalComision += $comi;

                $lineas[] = [
                    'factura_id' => $f->id,
                    'tipo' => 'factura',
                    'descripcion' => null,
                    'monto_base' => $base,
                    'porcentaje' => $porc,
                    'comision_calculada' => $comi,
                ];
            }
        } else {
            // Totales desde Pagos cobrados
            $pagos = Pago::query()
                ->where('cobrador_id', $empleado->id)
                ->whereBetween('fecha', [$d, $h])
                ->get(['id', 'factura_id', 'monto', 'fecha']);

            foreach ($pagos as $p) {
                $base = (float) $p->monto;
                if ($base <= 0)
                    continue;

                $comi = round(($base * $porc) / 100, 2);
                $totalBase += $base;
                $totalComision += $comi;

                $lineas[] = [
                    'factura_id' => $p->factura_id,
                    'tipo' => 'pago',
                    'descripcion' => 'Pago #' . $p->id,
                    'monto_base' => $base,
                    'porcentaje' => $porc,
                    'comision_calculada' => $comi,
                ];
            }
        }

        $ajustes = round($ajustes, 2);
        $totalCalculado = round($totalComision + $ajustes, 2);

        DB::transaction(function () use ($nomina, $periodo, $porc, $totalBase, $totalComision, $ajustes, $totalCalculado, $lineas) {
            $nomina
                ->fill([
                    'periodo' => $periodo,
                    'porcentaje_comision' => $porc,
                    'total_base' => round($totalBase, 2),
                    'total_comision' => round($totalComision, 2),
                    'ajustes' => $ajustes,
                    'total_calculado' => $totalCalculado,
                    'saldo' => round($totalCalculado - $nomina->total_pagado, 2),
                    'estado' => $nomina->total_pagado > 0 ? 'parcial' : 'borrador',
                ])
                ->save();

            // Regenerar snapshot
            $nomina->detalles()->whereIn('tipo', ['factura', 'pago'])->delete();
            if (!empty($lineas)) {
                $nomina->detalles()->createMany($lineas);
            }
        });

        return $nomina->load(['detalles', 'pagos', 'empleado']);
    }

    /**
     * Cerrar nómina. Si queda saldo 0 o menos, pasa a pagada y setea pagada_at.
     */
    public function cerrar(int $nominaId): Nomina
    {
        $nomina = Nomina::with(['detalles', 'pagos', 'empleado'])->findOrFail($nominaId);

        if (in_array($nomina->estado, ['cerrada', 'pagada'], true)) {
            abort(422, 'La nómina ya está cerrada o pagada.');
        }

        $nomina->estado = $nomina->total_pagado >= $nomina->total_calculado ? 'pagada' : 'cerrada';
        $nomina->cerrada_at = now();
        if ($nomina->estado === 'pagada') {
            $nomina->pagada_at = now();
        }
        $nomina->save();

        return $nomina;
    }

    /**
     * Registrar pago (parciales permitidos). Impide sobrepago.
     */
    public function pagar(int $nominaId, string $fecha, float $importe, ?string $metodo = null, ?string $nota = null): Nomina
    {
        $nomina = Nomina::with('pagos')->findOrFail($nominaId);

        if ($nomina->estado === 'pagada') {
            abort(422, 'La nómina ya está pagada.');
        }

        $totalCalculado = round((float) $nomina->total_calculado, 2);
        $totalPagado = round((float) $nomina->total_pagado, 2);
        $saldoDisponible = round($totalCalculado - $totalPagado, 2);

        if ($saldoDisponible <= 0) {
            abort(422, 'La nómina no tiene saldo pendiente.');
        }

        $importe = round($importe, 2);
        if ($importe > $saldoDisponible) {
            abort(422, 'El importe excede el saldo pendiente.');
        }

        DB::transaction(function () use ($nomina, $fecha, $importe, $metodo, $nota) {
            $pago = $nomina->pagos()->create([
                'fecha' => $fecha,
                'importe' => $importe,
                'metodo' => $metodo,
                'nota' => $nota,
            ]);
            Gasto::updateOrCreate(
                ['nomina_pago_id' => $pago->id],  // clave única (evita duplicados)
                [
                    'fecha' => $pago->fecha,
                    'categoria' => 'nomina',
                    'subcategoria' => null,
                    'monto' => $pago->importe,
                    'metodo_pago' => $pago->metodo,
                    'descripcion' => $pago->nota,
                    'empleado_id' => $nomina->empleado_id,
                ]
            );

            $nomina->total_pagado = round($nomina->total_pagado + $pago->importe, 2);
            $nomina->saldo = round($nomina->total_calculado - $nomina->total_pagado, 2);

            if ($nomina->saldo <= 0) {
                $nomina->estado = 'pagada';
                $nomina->pagada_at = now();
                $nomina->saldo = 0;  // normalizar
            } else {
                $nomina->estado = 'parcial';
            }
            $nomina->save();
        });

        return $nomina->load('pagos');
    }

    /**
     * Agregar un ajuste manual (+/-) y recalcular cabecera/estado.
     */
    public function agregarAjuste(int $nominaId, ?string $descripcion, float $importe): Nomina
    {
        $nomina = Nomina::with('detalles', 'pagos')->findOrFail($nominaId);

        if ($nomina->estado === 'pagada') {
            abort(422, 'La nómina está pagada. No admite ajustes.');
        }

        DB::transaction(function () use ($nomina, $descripcion, $importe) {
            $importe = round($importe, 2);

            $nomina->detalles()->create([
                'tipo' => 'ajuste',
                'descripcion' => $descripcion,
                'monto_base' => $importe,
                'porcentaje' => null,
                'comision_calculada' => $importe,
            ]);

            $ajustesSuma = (float) $nomina->detalles()->where('tipo', 'ajuste')->sum('comision_calculada');

            $nomina->ajustes = round($ajustesSuma, 2);
            $nomina->total_calculado = round($nomina->total_comision + $nomina->ajustes, 2);
            $nomina->saldo = round($nomina->total_calculado - $nomina->total_pagado, 2);

            if ($nomina->total_pagado >= $nomina->total_calculado) {
                $nomina->estado = 'pagada';
                $nomina->pagada_at = $nomina->pagada_at ?? now();
                $nomina->saldo = 0;
            } elseif ($nomina->total_pagado > 0) {
                $nomina->estado = 'parcial';
            } else {
                $nomina->estado = $nomina->estado === 'cerrada' ? 'cerrada' : 'borrador';
            }

            $nomina->save();
        });

        return $nomina->load('detalles');
    }

    /**
     * Detalle completo (con relaciones).
     */
    public function show(int $nominaId)
    {
        $nomina = Nomina::with(['empleado.vendedor', 'empleado.cobrador', 'detalles', 'pagos'])
            ->findOrFail($nominaId);

        // Determinar rol según el empleado
        if ($nomina->empleado->vendedor) {
            $rol = 'vendedor';
        } elseif ($nomina->empleado->cobrador) {
            $rol = 'cobrador';
        } else {
            $rol = 'otro';
        }

        // Agregar rol como atributo dinámico
        $nomina->rol = $rol;

        return $nomina;
    }

    /*
     * =========================
     * Helpers privados
     * =========================
     */

    /**
     * Chequea si existe solape de rangos para el empleado.
     * Solapa si: (desde_existente <= hasta_nueva) && (hasta_existente >= desde_nueva)
     */
    private function existeSolape(int $empleadoId, string $desde, string $hasta): bool
    {
        $q = Nomina::query()->where('empleado_id', $empleadoId)->whereDate('desde', '<=', $hasta)->whereDate('hasta', '>=', $desde);

        return $q->exists();
    }

    /**
     * Normaliza una nómina a un array “fila” apto para DataTables / JSON.
     */
    private function mapNominaFila(Nomina $n): array
    {
        return [
            'id' => $n->id,
            'empleado_id' => $n->empleado_id,
            'empleado' => $n->empleado?->nombre,
            'periodo' => $n->periodo,
            'desde' => $n->desde ? Carbon::parse($n->desde)->toDateString() : null,
            'hasta' => $n->hasta ? Carbon::parse($n->hasta)->toDateString() : null,
            'porcentaje_comision' => $n->porcentaje_comision,
            'total_base' => (float) $n->total_base,
            'total_comision' => (float) $n->total_comision,
            'ajustes' => (float) $n->ajustes,
            'total_calculado' => (float) $n->total_calculado,
            'total_pagado' => (float) $n->total_pagado,
            'saldo' => (float) $n->saldo,
            'estado' => $n->estado,
            'created_at' => $n->created_at,
            'updated_at' => $n->updated_at,
        ];
    }
}
