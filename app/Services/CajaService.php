<?php

namespace App\Services;

use App\Models\Credito;
use App\Models\GastoLeyma;
use App\Models\Inyeccion;
use App\Models\PagoCredito;
use App\Models\Recargo;
use Carbon\Carbon;

class CajaService
{
    /**
     * Calcular caja para un período específico
     */
    public function calcularCaja($fechaInicio, $fechaFin)
    {
        $entradas = $this->calcularEntradas($fechaInicio, $fechaFin);
        $salidas = $this->calcularSalidas($fechaInicio, $fechaFin);

        return [
            'entradas' => $entradas,
            'salidas' => $salidas,
            'caja' => $entradas['total'] - $salidas['total'],
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ];
    }

    /**
     * Calcular entradas de caja
     */
    private function calcularEntradas($fechaInicio, $fechaFin)
    {
        $pagos = PagoCredito::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])->sum('monto');
        $recargos = Recargo::whereBetween('fecha_aplicacion', [$fechaInicio, $fechaFin])->sum('monto');
        $inyecciones = Inyeccion::whereBetween('fecha', [$fechaInicio, $fechaFin])->sum('monto');

        return [
            'pagos' => $pagos,
            'recargos' => $recargos,
            'inyecciones' => $inyecciones,
            'total' => $pagos + $recargos + $inyecciones
        ];
    }

    /**
     * Calcular salidas de caja
     */
    private function calcularSalidas($fechaInicio, $fechaFin)
    {
        $prestamos = Credito::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])->sum('monto_principal');
        $gastosMauro = GastoLeyma::mauro()->whereBetween('fecha', [$fechaInicio, $fechaFin])->sum('monto');
        $gastosGota = GastoLeyma::gota()->whereBetween('fecha', [$fechaInicio, $fechaFin])->sum('monto');
        $inversiones = GastoLeyma::inversion()->whereBetween('fecha', [$fechaInicio, $fechaFin])->sum('monto');

        return [
            'prestamos' => $prestamos,
            'gastos_mauro' => $gastosMauro,
            'gastos_gota' => $gastosGota,
            'inversiones' => $inversiones,
            'total' => $prestamos + $gastosMauro + $gastosGota + $inversiones
        ];
    }

    /**
     * Obtener resumen de caja por períodos
     */
    public function obtenerResumenPorPeriodos()
    {
        $hoy = Carbon::now();

        return [
            'hoy' => $this->calcularCaja($hoy->copy()->startOfDay(), $hoy->copy()->endOfDay()),
            'semana' => $this->calcularCaja($hoy->copy()->subDays(6)->startOfDay(), $hoy->copy()->endOfDay()),
            'mes' => $this->calcularCaja($hoy->copy()->startOfMonth(), $hoy->copy()->endOfMonth()),
            'año' => $this->calcularCaja($hoy->copy()->startOfYear(), $hoy->copy()->endOfYear())
        ];
    }

    /**
     * Obtener flujo de caja por días del mes actual
     */
    public function obtenerFlujoMensual()
    {
        $hoy = Carbon::now();
        $diasEnMes = $hoy->daysInMonth;
        $flujo = [];

        for ($dia = 1; $dia <= $diasEnMes; $dia++) {
            $fecha = $hoy->copy()->day($dia);
            $caja = $this->calcularCaja($fecha->copy()->startOfDay(), $fecha->copy()->endOfDay());

            $flujo[] = [
                'dia' => $dia,
                'fecha' => $fecha->toDateString(),
                'entradas' => $caja['entradas']['total'],
                'salidas' => $caja['salidas']['total'],
                'caja' => $caja['caja']
            ];
        }

        return $flujo;
    }

    /**
     * Obtener tendencia de caja (últimos 30 días)
     */
    public function obtenerTendenciaCaja()
    {
        $hoy = Carbon::now();
        $tendencia = [];

        for ($i = 29; $i >= 0; $i--) {
            $fecha = $hoy->copy()->subDays($i);
            $caja = $this->calcularCaja($fecha->copy()->startOfDay(), $fecha->copy()->endOfDay());

            $tendencia[] = [
                'fecha' => $fecha->toDateString(),
                'caja' => $caja['caja'],
                'entradas' => $caja['entradas']['total'],
                'salidas' => $caja['salidas']['total']
            ];
        }

        return $tendencia;
    }
}