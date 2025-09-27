<?php

namespace App\Http\Controllers;

use App\Models\ClienteCredito;
use App\Models\Credito;
use App\Models\GastoLeyma;
use App\Models\Inyeccion;
use App\Models\PagoCredito;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditoDashboardController extends Controller
{
    /**
     * Obtener estadísticas completas del dashboard de créditos
     */
    public function estadisticas(Request $request)
    {
        try {
            $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->toDateString());
            $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->toDateString());

            // Estadísticas generales
            $creditosTotales = Credito::count();
            $creditosActivos = Credito::activos()->count();
            $creditosPagados = Credito::pagados()->count();
            $creditosVencidos = Credito::vencidos()->count();
            $montoTotalPrestado = Credito::sum('monto_principal') ?? 0;
            $montoTotalPagado = PagoCredito::sum('monto') ?? 0;
            $montoPendiente = $montoTotalPrestado - $montoTotalPagado;
            $clientesActivos = ClienteCredito::whereHas('creditos', function ($query) {
                $query->activos();
            })->count();

            // Estadísticas de la caja
            $totalCobrado = PagoCredito::sum('monto') ?? 0;
            $totalGastado = GastoLeyma::sum('monto') ?? 0;
            $totalInyecciones = Inyeccion::sum('monto') ?? 0;
            $saldoCaja = $totalCobrado + $totalInyecciones - $totalGastado - $montoTotalPrestado;

            $estadisticas = [
                'creditos_totales' => $creditosTotales,
                'creditos_activos' => $creditosActivos,
                'creditos_pagados' => $creditosPagados,
                'creditos_vencidos' => $creditosVencidos,
                'monto_total_prestado' => $montoTotalPrestado,
                'monto_total_pagado' => $montoTotalPagado,
                'monto_pendiente' => $montoPendiente,
                'clientes_activos' => $clientesActivos,
                // Estadísticas de la caja
                'total_cobrado' => $totalCobrado,
                'total_gastado' => $totalGastado,
                'total_inyecciones' => $totalInyecciones,
                'saldo_caja' => $saldoCaja,
            ];

            // Calcular porcentajes
            $estadisticas['porcentaje_pagado'] = $montoTotalPrestado > 0
                ? round(($montoTotalPagado / $montoTotalPrestado) * 100, 2)
                : 0;

            $estadisticas['porcentaje_activos'] = $creditosTotales > 0
                ? round(($creditosActivos / $creditosTotales) * 100, 2)
                : 0;

            // Estadísticas por tipo de crédito
            $estadisticas['por_tipo'] = Credito::select('tipo_pago', DB::raw('count(*) as cantidad'), DB::raw('sum(monto_principal) as monto_total'))
                ->groupBy('tipo_pago')
                ->get()
                ->map(function ($item) use ($creditosTotales) {
                    return [
                        'tipo' => ucfirst($item->tipo_pago),
                        'cantidad' => $item->cantidad,
                        'monto_total' => $item->monto_total,
                        'porcentaje' => $creditosTotales > 0
                            ? round(($item->cantidad / $creditosTotales) * 100, 2)
                            : 0
                    ];
                });

            // Estadísticas por mes (últimos 6 meses)
            $estadisticas['por_mes'] = Credito::select(
                DB::raw('DATE_FORMAT(fecha_inicio, "%Y-%m") as mes'),
                DB::raw('count(*) as cantidad'),
                DB::raw('sum(monto_principal) as monto_total')
            )
                ->where('fecha_inicio', '>=', now()->subMonths(6))
                ->groupBy('mes')
                ->orderBy('mes')
                ->get()
                ->map(function ($item) {
                    return [
                        'mes' => Carbon::createFromFormat('Y-m', $item->mes)->format('M Y'),
                        'cantidad' => $item->cantidad,
                        'monto_total' => $item->monto_total
                    ];
                });

            // Top 5 clientes con más créditos
            $estadisticas['top_clientes'] = ClienteCredito::select(
                'clientes_credito.id',
                'clientes_credito.nombre',
                'clientes_credito.dni',
                DB::raw('count(creditos.id) as total_creditos'),
                DB::raw('sum(creditos.monto_principal) as monto_total')
            )
                ->join('creditos', 'clientes_credito.id', '=', 'creditos.cliente_id')
                ->groupBy('clientes_credito.id', 'clientes_credito.nombre', 'clientes_credito.dni')
                ->orderBy('total_creditos', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($cliente) {
                    return [
                        'id' => $cliente->id,
                        'nombre' => $cliente->nombre,
                        'dni' => $cliente->dni,
                        'total_creditos' => $cliente->total_creditos,
                        'monto_total' => $cliente->monto_total
                    ];
                });

            // Créditos próximos a vencer (próximos 7 días)
            $estadisticas['proximos_vencer'] = Credito::activos()
                ->where('fecha_vencimiento', '<=', now()->addDays(7))
                ->where('fecha_vencimiento', '>=', now())
                ->with('cliente')
                ->orderBy('fecha_vencimiento')
                ->limit(5)
                ->get()
                ->map(function ($credito) {
                    $diasRestantes = now()->diffInDays($credito->fecha_vencimiento, false);
                    return [
                        'id' => $credito->id,
                        'cliente_nombre' => $credito->cliente ? $credito->cliente->nombre : 'Cliente no encontrado',
                        'monto_principal' => $credito->monto_principal,
                        'fecha_final' => $credito->fecha_vencimiento->format('d/m/Y'),
                        'dias_restantes' => $diasRestantes,
                        'urgente' => $diasRestantes <= 3
                    ];
                });

            // Estadísticas de pagos por mes (últimos 6 meses)
            $estadisticas['pagos_por_mes'] = PagoCredito::select(
                DB::raw('DATE_FORMAT(fecha_pago, "%Y-%m") as mes'),
                DB::raw('count(*) as cantidad_pagos'),
                DB::raw('sum(monto) as monto_total')
            )
                ->where('fecha_pago', '>=', now()->subMonths(6))
                ->groupBy('mes')
                ->orderBy('mes')
                ->get()
                ->map(function ($item) {
                    return [
                        'mes' => Carbon::createFromFormat('Y-m', $item->mes)->format('M Y'),
                        'cantidad_pagos' => $item->cantidad_pagos,
                        'monto_total' => $item->monto_total
                    ];
                });

            // Promedio de días de crédito
            $estadisticas['promedio_dias_credito'] = Credito::avg('dias_credito') ?? 0;

            // Tasa de recuperación (créditos pagados vs total)
            $estadisticas['tasa_recuperacion'] = $creditosTotales > 0
                ? round(($creditosPagados / $creditosTotales) * 100, 2)
                : 0;

            return response()->json($estadisticas);
        } catch (\Exception $e) {
            \Log::error('Error en dashboard estadísticas: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al cargar estadísticas',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos para gráficos
     */
    public function datosGraficos(Request $request)
    {
        try {
            $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->toDateString());
            $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->toDateString());

            // Datos para gráfico de dona - Distribución por tipo
            $distribucionTipo = Credito::select('tipo_pago', DB::raw('count(*) as cantidad'))
                ->groupBy('tipo_pago')
                ->get()
                ->map(function ($item) {
                    return [
                        'label' => ucfirst($item->tipo_pago),
                        'value' => $item->cantidad,
                        'color' => $this->getColorForType($item->tipo_pago)
                    ];
                });

            // Datos para gráfico de barras - Créditos por mes (año actual)
            $anioActual = now()->year;
            $creditosPorMes = Credito::select(
                DB::raw('DATE_FORMAT(fecha_inicio, "%Y-%m") as mes'),
                DB::raw('count(*) as cantidad')
            )
                ->whereYear('fecha_inicio', $anioActual)
                ->groupBy('mes')
                ->orderBy('mes')
                ->get()
                ->map(function ($item) {
                    return [
                        'mes' => Carbon::createFromFormat('Y-m', $item->mes)->format('M'),
                        'cantidad' => $item->cantidad
                    ];
                });

            // Asegurar que todos los meses del año estén presentes
            $mesesCompletos = [];
            for ($i = 1; $i <= 12; $i++) {
                $mes = str_pad($i, 2, '0', STR_PAD_LEFT);
                $mesKey = $anioActual . '-' . $mes;
                $mesNombre = Carbon::createFromFormat('Y-m', $mesKey)->format('M');

                $existe = $creditosPorMes->firstWhere('mes', $mesNombre);
                $mesesCompletos[] = [
                    'mes' => $mesNombre,
                    'cantidad' => $existe ? $existe['cantidad'] : 0
                ];
            }
            $creditosPorMes = collect($mesesCompletos);

            // Datos para gráfico de líneas - Montos por mes (año actual)
            $montosPorMes = Credito::select(
                DB::raw('DATE_FORMAT(fecha_inicio, "%Y-%m") as mes'),
                DB::raw('sum(monto_principal) as monto_total')
            )
                ->whereYear('fecha_inicio', $anioActual)
                ->groupBy('mes')
                ->orderBy('mes')
                ->get()
                ->map(function ($item) {
                    return [
                        'mes' => Carbon::createFromFormat('Y-m', $item->mes)->format('M'),
                        'monto' => $item->monto_total
                    ];
                });

            // Asegurar que todos los meses del año estén presentes para montos
            $montosCompletos = [];
            for ($i = 1; $i <= 12; $i++) {
                $mes = str_pad($i, 2, '0', STR_PAD_LEFT);
                $mesKey = $anioActual . '-' . $mes;
                $mesNombre = Carbon::createFromFormat('Y-m', $mesKey)->format('M');

                $existe = $montosPorMes->firstWhere('mes', $mesNombre);
                $montosCompletos[] = [
                    'mes' => $mesNombre,
                    'monto' => $existe ? $existe['monto'] : 0
                ];
            }
            $montosPorMes = collect($montosCompletos);

            return response()->json([
                'distribucion_tipo' => $distribucionTipo,
                'creditos_por_mes' => $creditosPorMes,
                'montos_por_mes' => $montosPorMes
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en dashboard gráficos: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al cargar gráficos',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener color para cada tipo de crédito
     */
    private function getColorForType($tipo)
    {
        $colores = [
            'diario' => '#28a745',
            'semanal' => '#007bff',
            'quincenal' => '#ffc107',
            'mensual' => '#dc3545'
        ];

        return $colores[$tipo] ?? '#6c757d';
    }
}
