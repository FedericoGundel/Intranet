<?php

namespace App\Services;

use App\Models\Credito;
use App\Models\Descuento;
use App\Models\Inyeccion;
use App\Models\PagoCredito;
use App\Models\PrestamoFamiliar;
use App\Models\Recargo;
use Carbon\Carbon;

class CierreService
{
    /**
     * Generar cierre del período
     */
    public function generarCierre($fechaInicio, $fechaFin)
    {
        return [
            'periodo' => [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin
            ],
            'inyeccion' => $this->calcularInyeccion($fechaInicio, $fechaFin),
            'recargos' => $this->calcularRecargos($fechaInicio, $fechaFin),
            'descuentos' => $this->calcularDescuentos($fechaInicio, $fechaFin),
            'prestamos_familiares' => $this->calcularPrestamosFamiliares($fechaInicio, $fechaFin),
            'total_prestado' => $this->calcularTotalPrestado($fechaInicio, $fechaFin),
            'total_cobrado' => $this->calcularTotalCobrado($fechaInicio, $fechaFin),
            'total_ganancia' => $this->calcularGanancia($fechaInicio, $fechaFin),
            'resumen_creditos' => $this->obtenerResumenCreditos($fechaInicio, $fechaFin)
        ];
    }

    /**
     * Calcular inyección de capital
     */
    private function calcularInyeccion($fechaInicio, $fechaFin)
    {
        return Inyeccion::whereBetween('fecha', [$fechaInicio, $fechaFin])->sum('monto');
    }

    /**
     * Calcular recargos aplicados
     */
    private function calcularRecargos($fechaInicio, $fechaFin)
    {
        return Recargo::whereBetween('fecha_aplicacion', [$fechaInicio, $fechaFin])->sum('monto');
    }

    /**
     * Calcular descuentos aplicados
     */
    private function calcularDescuentos($fechaInicio, $fechaFin)
    {
        return Descuento::whereBetween('fecha_aplicacion', [$fechaInicio, $fechaFin])->sum('monto');
    }

    /**
     * Calcular préstamos a familiares
     */
    private function calcularPrestamosFamiliares($fechaInicio, $fechaFin)
    {
        return PrestamoFamiliar::whereBetween('fecha_prestamo', [$fechaInicio, $fechaFin])->sum('monto');
    }

    /**
     * Calcular total prestado
     */
    private function calcularTotalPrestado($fechaInicio, $fechaFin)
    {
        return Credito::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])->sum('monto_principal');
    }

    /**
     * Calcular total cobrado
     */
    private function calcularTotalCobrado($fechaInicio, $fechaFin)
    {
        return PagoCredito::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])->sum('monto');
    }

    /**
     * Calcular ganancia total (intereses + recargos)
     */
    private function calcularGanancia($fechaInicio, $fechaFin)
    {
        $intereses = Credito::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])->get()->sum(function ($credito) {
            return $credito->monto_total - $credito->monto_principal;
        });
        $recargos = $this->calcularRecargos($fechaInicio, $fechaFin);

        return $intereses + $recargos;
    }

    /**
     * Obtener resumen de créditos por tipo
     */
    private function obtenerResumenCreditos($fechaInicio, $fechaFin)
    {
        $creditos = Credito::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
            ->selectRaw('tipo_pago, COUNT(*) as cantidad, SUM(monto_principal) as total_prestado, SUM(monto_total) as total_cobrado')
            ->groupBy('tipo_pago')
            ->get();

        return $creditos->map(function ($credito) {
            return [
                'tipo' => $credito->tipo_pago,
                'cantidad' => $credito->cantidad,
                'total_prestado' => $credito->total_prestado,
                'total_cobrado' => $credito->total_cobrado,
                'ganancia' => $credito->total_cobrado - $credito->total_prestado
            ];
        });
    }

    /**
     * Generar cierre mensual
     */
    public function generarCierreMensual($mes = null, $año = null)
    {
        $fecha = Carbon::now();
        if ($mes)
            $fecha->month($mes);
        if ($año)
            $fecha->year($año);

        $fechaInicio = $fecha->copy()->startOfMonth();
        $fechaFin = $fecha->copy()->endOfMonth();

        return $this->generarCierre($fechaInicio, $fechaFin);
    }

    /**
     * Generar cierre anual
     */
    public function generarCierreAnual($año = null)
    {
        $fecha = Carbon::now();
        if ($año)
            $fecha->year($año);

        $fechaInicio = $fecha->copy()->startOfYear();
        $fechaFin = $fecha->copy()->endOfYear();

        return $this->generarCierre($fechaInicio, $fechaFin);
    }

    /**
     * Comparar períodos
     */
    public function compararPeriodos($periodoActual, $periodoAnterior)
    {
        $cierreActual = $this->generarCierre($periodoActual['inicio'], $periodoActual['fin']);
        $cierreAnterior = $this->generarCierre($periodoAnterior['inicio'], $periodoAnterior['fin']);

        return [
            'actual' => $cierreActual,
            'anterior' => $cierreAnterior,
            'comparacion' => [
                'inyeccion' => $cierreActual['inyeccion'] - $cierreAnterior['inyeccion'],
                'total_prestado' => $cierreActual['total_prestado'] - $cierreAnterior['total_prestado'],
                'total_cobrado' => $cierreActual['total_cobrado'] - $cierreAnterior['total_cobrado'],
                'total_ganancia' => $cierreActual['total_ganancia'] - $cierreAnterior['total_ganancia']
            ]
        ];
    }
}
