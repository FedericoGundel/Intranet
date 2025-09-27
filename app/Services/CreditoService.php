<?php

namespace App\Services;

use App\Models\ClienteCredito;
use App\Models\ConfiguracionIntereses;
use App\Models\Credito;
use App\Models\PagoCredito;
use App\Models\TipoCredito;
use Carbon\Carbon;

class CreditoService
{
    /**
     * Calcular intereses según el tipo de crédito y duración
     */
    public function calcularIntereses($montoPrincipal, $tipoCredito, $duracionDias)
    {
        $configuracion = ConfiguracionIntereses::where('tipo_credito', $tipoCredito)
            ->where('duracion_dias', $duracionDias)
            ->where('activo', true)
            ->first();

        if (!$configuracion) {
            // Calcular basado en el tipo de crédito
            return $this->calcularInteresesPorTipo($montoPrincipal, $tipoCredito, $duracionDias);
        }

        $interes = ($montoPrincipal * $configuracion->interes_porcentaje) / 100;
        return round($interes, 2);
    }

    /**
     * Calcular intereses por tipo de crédito
     */
    private function calcularInteresesPorTipo($montoPrincipal, $tipoCredito, $duracionDias)
    {
        switch ($tipoCredito) {
            case 'diario':
                // 10% por semana, máximo 80% en 2 meses (52 días)
                $semanas = ceil($duracionDias / 7);
                $porcentajeInteres = min($semanas * 10, 80);
                break;

            case 'semanal':
                // 10% por semana, máximo 80% en 2 meses (8 semanas)
                $semanas = $duracionDias / 7;
                $porcentajeInteres = min($semanas * 10, 80);
                break;

            case 'quincenal':
                // 10% por semana, máximo 80% en 2 meses (4 quincenas)
                $semanas = ($duracionDias / 7);
                $porcentajeInteres = min($semanas * 10, 80);
                break;

            case 'contado':
                // Para créditos de contado, el interés se calcula diferente
                $dias = $duracionDias;
                $porcentajeInteres = min(($dias / 7) * 10, 80);
                break;

            default:
                $porcentajeInteres = 0;
        }

        $interes = ($montoPrincipal * $porcentajeInteres) / 100;
        return round($interes, 2);
    }

    /**
     * Crear un nuevo crédito
     */
    public function crearCredito($datos)
    {
        // Validar que el cliente no tenga crédito activo
        $cliente = ClienteCredito::find($datos['cliente_id']);
        if ($cliente->tieneCreditoActivo()) {
            throw new \Exception('El cliente ya tiene un crédito activo');
        }

        // Obtener tipo de crédito
        $tipoCredito = TipoCredito::find($datos['tipo_credito_id']);

        // Calcular duración en días
        $duracionDias = $this->calcularDuracionDias($datos, $tipoCredito);

        // Calcular intereses
        $interes = $this->calcularIntereses(
            $datos['monto_principal'],
            $tipoCredito->nombre,
            $duracionDias
        );

        // Calcular monto total
        $montoTotal = $datos['monto_principal'] + $interes;

        // Generar número de crédito
        $numeroCredito = $this->generarNumeroCredito();

        // Crear el crédito
        $credito = Credito::create([
            'numero_credito' => $numeroCredito,
            'cliente_id' => $datos['cliente_id'],
            'garante_id' => $datos['garante_id'] ?? null,
            'tipo_credito_id' => $datos['tipo_credito_id'],
            'monto_principal' => $datos['monto_principal'],
            'monto_total' => $montoTotal,
            'interes_porcentaje' => ($interes / $datos['monto_principal']) * 100,
            'cantidad_cuotas' => $datos['cantidad_cuotas'],
            'fecha_inicio' => $datos['fecha_inicio'],
            'fecha_vencimiento' => $datos['fecha_final'] ?? null,
            'dia_pago' => $datos['dia_pago'] ?? null,
            'usuario_id' => auth()->id(),
            'observaciones' => $datos['observaciones'] ?? null
        ]);

        // La fecha de vencimiento se calcula dinámicamente en el modelo

        // Generar cuotas si no es crédito de contado
        if ($tipoCredito->nombre !== 'contado') {
            $credito->generarCuotas();
        }

        return $credito;
    }

    /**
     * Calcular duración en días según el tipo de crédito
     */
    private function calcularDuracionDias($datos, $tipoCredito)
    {
        switch ($tipoCredito->nombre) {
            case 'diario':
                return $datos['cantidad_cuotas'];  // Los días son las cuotas

            case 'semanal':
                return $datos['cantidad_cuotas'] * 7;

            case 'quincenal':
                return $datos['cantidad_cuotas'] * 14;

            case 'contado':
                $fechaInicio = Carbon::parse($datos['fecha_inicio']);
                $fechaFinal = Carbon::parse($datos['fecha_final']);
                return $fechaInicio->diffInDays($fechaFinal);

            default:
                return $datos['cantidad_cuotas'];
        }
    }

    /**
     * Generar número de crédito único
     */
    private function generarNumeroCredito()
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
     * Registrar pago de crédito
     */
    public function registrarPago($creditoId, $monto, $fechaPago = null, $metodoPago = null, $observaciones = null)
    {
        $credito = Credito::findOrFail($creditoId);

        if ($credito->tipoCredito->nombre === 'contado') {
            // Para créditos de contado, el pago es directo al crédito
            return $this->registrarPagoContado($credito, $monto, $fechaPago, $metodoPago, $observaciones);
        } else {
            // Para créditos con cuotas, buscar la próxima cuota pendiente
            $cuota = $credito->cuotas()->where('estado', '!=', 'pagada')->orderBy('numero_cuota')->first();

            if (!$cuota) {
                throw new \Exception('No hay cuotas pendientes para este crédito');
            }

            return $cuota->registrarPago($monto, $fechaPago, $metodoPago, $observaciones);
        }
    }

    /**
     * Registrar pago para crédito de contado
     */
    private function registrarPagoContado($credito, $monto, $fechaPago, $metodoPago, $observaciones)
    {
        $fechaPago = $fechaPago ?: Carbon::now();

        // Crear el pago
        $pago = $credito->pagos()->create([
            'monto' => $monto,
            'fecha_pago' => $fechaPago,
            'metodo_pago' => $metodoPago,
            'observaciones' => $observaciones,
            'usuario_id' => auth()->id()
        ]);

        // Actualizar el saldo del crédito
        $credito->actualizarSaldo();

        return $pago;
    }

    /**
     * Obtener estadísticas de créditos
     */
    public function obtenerEstadisticas($fechaInicio = null, $fechaFin = null)
    {
        $fechaInicio = $fechaInicio ?: Carbon::now()->startOfMonth();
        $fechaFin = $fechaFin ?: Carbon::now()->endOfMonth();

        return [
            'creditos_activos' => Credito::activos()->count(),
            'creditos_vencidos' => Credito::vencidos()->count(),
            'creditos_por_vencer' => Credito::where('fecha_vencimiento', '>', now())
                ->where('fecha_vencimiento', '<=', now()->addDays(7))
                ->whereDoesntHave('pagos')
                ->count(),
            'monto_total_prestado' => Credito::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])->sum('monto_principal'),
            'monto_total_cobrado' => PagoCredito::whereHas('credito', function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin]);
            })->sum('monto'),
            'monto_pendiente_cobro' => Credito::activos()->get()->sum(function ($credito) {
                return $credito->saldo_pendiente;
            }),
            'ingresos_por_intereses' => Credito::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])->get()->sum(function ($credito) {
                return $credito->monto_total - $credito->monto_principal;
            })
        ];
    }
}
