<?php

namespace App\Http\Controllers;

use App\Http\Controllers\FacturasController;
use App\Models\Factura;
use App\Models\Gasto;
use App\Models\Pago;
use App\Services\FacturaService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Obtener totales de todos los tiempos
        $totalGastos = Gasto::sum('monto');
        $totalCobros = Pago::sum('monto');

        return view('home', compact('totalGastos', 'totalCobros'));
    }

    public function facturas(Request $request)
    {
        $facturas = app(FacturaService::class)->listarFacturasCobros();
        return response()->json(['data' => $facturas]);
    }

    public function caja(Request $request)
    {
        $intervalo = $request->query('intervalo', 'anio');
        $hoy = now();

        switch ($intervalo) {
            case 'hoy':
                $inicio = $hoy->copy()->startOfDay();
                break;
            case 'semana':
                $inicio = $hoy->copy()->subDays(6)->startOfDay();
                break;
            case 'mes':
                $inicio = $hoy->copy()->startOfMonth();
                break;
            case 'anio':
            default:
                $inicio = $hoy->copy()->startOfYear();
                break;
        }

        // ===============================
        // Bloque principal (intervalo)
        // ===============================
        $totalCobrado = Pago::whereBetween('fecha', [$inicio, $hoy])->sum('monto');
        $totalGastado = Gasto::whereBetween('fecha', [$inicio, $hoy])->sum('monto');

        $totalFacturado = Factura::whereBetween('fecha', [$inicio, $hoy])
            ->withSum('articuloFacturas as total_facturado', 'total')
            ->get()
            ->sum('total_facturado');

        $totalPagado = Pago::whereBetween('fecha', [$inicio, $hoy])->sum('monto');
        $ventasPorCobrar = max(0, $totalFacturado - $totalPagado);

        // ===============================
        // Bloque "ayer"
        // ===============================
        $ayerInicio = $hoy->copy()->subDay()->startOfDay();
        $ayerFin = $hoy->copy()->subDay()->endOfDay();

        $totalCobradoAyer = Pago::whereBetween('fecha', [$ayerInicio, $ayerFin])->sum('monto');
        $totalGastadoAyer = Gasto::whereBetween('fecha', [$ayerInicio, $ayerFin])->sum('monto');

        $totalFacturadoAyer = Factura::whereBetween('fecha', [$ayerInicio, $ayerFin])
            ->withSum('articuloFacturas as total_facturado', 'total')
            ->get()
            ->sum('total_facturado');

        $totalPagadoAyer = Pago::whereBetween('fecha', [$ayerInicio, $ayerFin])->sum('monto');
        $ventasPorCobrarAyer = max(0, $totalFacturadoAyer - $totalPagadoAyer);

        return response()->json([
            'totalCobrado' => round($totalCobrado, 2),
            'totalGastado' => round($totalGastado, 2),
            'ventasPorCobrar' => round($ventasPorCobrar, 2),
            'ayer' => [
                'totalCobrado' => round($totalCobradoAyer, 2),
                'totalGastado' => round($totalGastadoAyer, 2),
                'ventasPorCobrar' => round($ventasPorCobrarAyer, 2),
            ],
        ]);
    }
}