<?php

namespace App\Services;

use App\Models\ClienteCredito;
use App\Models\ConfiguracionCredito;
use App\Models\Credito;
use App\Models\Descuento;
use App\Models\PagoCredito;
use App\Models\Recargo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LeymaCreditoService
{
    /**
     * Crear un nuevo crédito
     */
    public function crearCredito(array $datos): Credito
    {
        return DB::transaction(function () use ($datos) {
            // Validar que el cliente no tenga crédito activo
            $cliente = ClienteCredito::findOrFail($datos['cliente_id']);
            if ($cliente->tieneCreditoActivo()) {
                throw ValidationException::withMessages([
                    'cliente_id' => 'El cliente ya tiene un crédito activo'
                ]);
            }

            // Usar tipo_pago directamente (ya viene como string)
            $tipoPago = $datos['tipo_pago'] ?? 'diario';

            // Obtener configuración del tipo de pago
            $config = ConfiguracionCredito::getConfiguracion($tipoPago);
            if (!$config) {
                throw ValidationException::withMessages([
                    'tipo_pago' => 'Tipo de crédito no válido'
                ]);
            }

            // Usar porcentaje_base directamente del formulario
            $porcentajeBase = $datos['porcentaje_base'] ?? 15.0;

            // Preparar datos del crédito
            $datosCredito = [
                'cliente_id' => $datos['cliente_id'],
                'numero_credito' => $this->generarNumeroCredito(),
                'monto_principal' => $datos['monto_principal'],
                'tipo_pago' => $tipoPago,
                'porcentaje_base' => $porcentajeBase,
                'cantidad_cuotas' => $datos['cantidad_cuotas'],
                'fecha_inicio' => $datos['fecha_inicio'],
                'observaciones' => $datos['observaciones'] ?? null,
                'usuario_id' => Auth::id() ?? 1
            ];

            // Para créditos contado, agregar fecha_vencimiento (fecha de pago único)
            if ($tipoPago === 'contado' && isset($datos['fecha_final'])) {
                $datosCredito['fecha_vencimiento'] = $datos['fecha_final'];
            }

            // Crear el crédito
            $credito = Credito::create($datosCredito);

            return $credito;
        });
    }

    /**
     * Calcular días de crédito basado en tipo y cantidad de cuotas
     */
    private function calcularDiasCredito(string $tipoPago, int $cantidadCuotas): int
    {
        switch ($tipoPago) {
            case 'diario':
                return $cantidadCuotas;
            case 'semanal':
                return $cantidadCuotas * 7;
            case 'quincenal':
                return $cantidadCuotas * 15;
            case 'contado':
                return 7;  // Por defecto 7 días para contado
            default:
                return $cantidadCuotas;
        }
    }

    /**
     * Calcular monto total del crédito
     */
    public function calcularMontoTotal(
        float $montoPrincipal,
        float $porcentajeFinal
    ): float {
        $montoInteres = $montoPrincipal * ($porcentajeFinal / 100);
        return $montoPrincipal + $montoInteres;
    }

    /**
     * Aplicar recargo a un crédito
     */
    public function aplicarRecargo(int $creditoId, float $monto, string $concepto, ?string $observaciones = null): Recargo
    {
        return DB::transaction(function () use ($creditoId, $monto, $concepto, $observaciones) {
            $recargo = Recargo::create([
                'credito_id' => $creditoId,
                'monto' => $monto,
                'concepto' => $concepto,
                'fecha_aplicacion' => Carbon::now()->toDateString(),
                'observaciones' => $observaciones,
                'usuario_id' => Auth::id() ?? 1
            ]);

            // Actualizar el crédito
            $recargo->actualizarCredito();

            return $recargo;
        });
    }

    /**
     * Aplicar descuento a un crédito
     */
    public function aplicarDescuento(int $creditoId, float $monto, string $concepto, ?string $observaciones = null): Descuento
    {
        return DB::transaction(function () use ($creditoId, $monto, $concepto, $observaciones) {
            $descuento = Descuento::create([
                'credito_id' => $creditoId,
                'monto' => $monto,
                'concepto' => $concepto,
                'fecha_aplicacion' => Carbon::now()->toDateString(),
                'observaciones' => $observaciones,
                'usuario_id' => Auth::id() ?? 1
            ]);

            // Actualizar el crédito
            $descuento->actualizarCredito();

            return $descuento;
        });
    }

    /**
     * Registrar pago de crédito
     */
    public function registrarPago(int $creditoId, float $monto, string $fechaPago, ?string $metodoPago = null, ?string $observaciones = null, ?string $cuotasAfectadas = null, ?string $tipoPago = null)
    {
        return DB::transaction(function () use ($creditoId, $monto, $fechaPago, $metodoPago, $observaciones, $cuotasAfectadas, $tipoPago) {
            \Log::info('LeymaCreditoService::registrarPago - Datos recibidos:', [
                'creditoId' => $creditoId,
                'monto' => $monto,
                'fechaPago' => $fechaPago,
                'metodoPago' => $metodoPago,
                'observaciones' => $observaciones,
                'cuotasAfectadas' => $cuotasAfectadas,
                'tipoPago' => $tipoPago
            ]);
            // Cargar relaciones necesarias para calcular correctamente el saldo pendiente
            $credito = Credito::with(['recargos', 'descuentos'])->findOrFail($creditoId);

            // Permitir registrar pagos independientemente del estado del crédito

            // Verificar que el pago no exceda el saldo
            if ($monto > $credito->saldo_pendiente) {
                throw ValidationException::withMessages([
                    'monto' => 'El monto excede el saldo pendiente'
                ]);
            }

            // Crear el pago
            $pago = $credito->pagos()->create([
                'monto' => $monto,
                'fecha_pago' => $fechaPago,
                'metodo_pago' => $metodoPago,
                'observaciones' => $observaciones,
                'cuotas_afectadas' => $cuotasAfectadas,
                'tipo_pago' => $tipoPago,
                'usuario_id' => Auth::id() ?? 1
            ]);

            // Actualizar el crédito
            $credito->actualizarSaldo();

            return $pago;
        });
    }

    /**
     * Generar número de crédito único
     */
    private function generarNumeroCredito(): string
    {
        $prefijo = 'CR';
        $año = date('Y');
        $ultimoCredito = Credito::whereYear('created_at', $año)
            ->orderBy('id', 'desc')
            ->first();

        $numero = $ultimoCredito ? $ultimoCredito->id + 1 : 1;

        return $prefijo . $año . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Obtener estadísticas de créditos
     */
    public function obtenerEstadisticas($fechaInicio = null, $fechaFin = null)
    {
        $fechaInicio = $fechaInicio ?: Carbon::now()->startOfMonth();
        $fechaFin = $fechaFin ?: Carbon::now()->endOfMonth();

        // Calcular monto total cobrado usando la relación con pagos
        $montoTotalCobrado = PagoCredito::whereHas('credito', function ($query) use ($fechaInicio, $fechaFin) {
            $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin]);
        })->sum('monto');

        // Calcular monto pendiente de cobro (suma de saldos pendientes de créditos activos)
        $montoPendienteCobro = Credito::activos()->get()->sum(function ($credito) {
            return $credito->saldo_pendiente;
        });

        return [
            'creditos_totales' => Credito::count(),
            'creditos_activos' => Credito::activos()->count(),
            'creditos_vencidos' => Credito::vencidos()->count(),
            'creditos_pagados' => Credito::pagados()->count(),
            'monto_total_prestado' => (float) Credito::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])->sum('monto_principal'),
            'monto_total_cobrado' => (float) $montoTotalCobrado,
            'monto_pendiente_cobro' => (float) $montoPendienteCobro,
            'ingresos_por_intereses' => (float) Credito::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])->get()->sum(function ($credito) {
                return $credito->monto_total - $credito->monto_principal;
            })
        ];
    }

    /**
     * Recalcular un crédito después de modificar pagos
     */
    public function recalcularCredito(int $creditoId)
    {
        $credito = Credito::findOrFail($creditoId);

        // Ya no necesitamos actualizar campos en la base de datos
        // Los valores se calculan dinámicamente con los accessors
        // Este método se mantiene por compatibilidad pero no hace nada
        return $credito;
    }
}
