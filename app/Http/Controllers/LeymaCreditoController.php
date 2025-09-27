<?php

namespace App\Http\Controllers;

use App\Models\ClienteCredito;
use App\Models\ConfiguracionCredito;
use App\Models\Credito;
use App\Services\CajaService;
use App\Services\CierreService;
use App\Services\LeymaCreditoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeymaCreditoController extends Controller
{
    public function __construct(
        private LeymaCreditoService $leymaService,
        private CajaService $cajaService,
        private CierreService $cierreService
    ) {}

    /**
     * Vista principal del sistema Leyma Créditos
     */
    public function index()
    {
        return view('leyma-creditos.index');
    }

    /**
     * Obtener datos de caja
     */
    public function caja(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->toDateString());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->toDateString());

        $caja = $this->cajaService->calcularCaja($fechaInicio, $fechaFin);

        return response()->json($caja);
    }

    /**
     * Obtener cierre del período
     */
    public function cierre(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->toDateString());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->toDateString());

        $cierre = $this->cierreService->generarCierre($fechaInicio, $fechaFin);

        return response()->json($cierre);
    }

    /**
     * Obtener estadísticas generales
     */
    public function estadisticas(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->toDateString());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->toDateString());

        $estadisticas = $this->leymaService->obtenerEstadisticas($fechaInicio, $fechaFin);

        return response()->json($estadisticas);
    }

    /**
     * Obtener configuraciones de créditos
     */
    public function configuraciones()
    {
        $configuraciones = ConfiguracionCredito::activos()->get();

        return response()->json($configuraciones);
    }

    /**
     * Aplicar recargo a un crédito
     */
    public function aplicarRecargo(Request $request)
    {
        $request->validate([
            'credito_id' => 'required|exists:creditos,id',
            'monto' => 'required|numeric|min:0.01',
            'concepto' => 'required|string|max:255',
            'observaciones' => 'nullable|string'
        ]);

        try {
            $recargo = $this->leymaService->aplicarRecargo(
                $request->credito_id,
                $request->monto,
                $request->concepto,
                $request->observaciones
            );

            return response()->json([
                'success' => true,
                'message' => 'Recargo aplicado correctamente',
                'recargo' => $recargo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Aplicar descuento a un crédito
     */
    public function aplicarDescuento(Request $request)
    {
        $request->validate([
            'credito_id' => 'required|exists:creditos,id',
            'monto' => 'required|numeric|min:0.01',
            'concepto' => 'required|string|max:255',
            'observaciones' => 'nullable|string'
        ]);

        try {
            $descuento = $this->leymaService->aplicarDescuento(
                $request->credito_id,
                $request->monto,
                $request->concepto,
                $request->observaciones
            );

            return response()->json([
                'success' => true,
                'message' => 'Descuento aplicado correctamente',
                'descuento' => $descuento
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Registrar pago de crédito
     */
    public function registrarPago(Request $request)
    {
        \Log::info('RegistrarPago - Datos recibidos:', $request->all());
        \Log::info('RegistrarPago - cuotas_afectadas: ' . $request->input('cuotas_afectadas'));
        \Log::info('RegistrarPago - tipo_pago: ' . $request->input('tipo_pago'));

        $request->validate([
            'credito_id' => 'required|exists:creditos,id',
            'monto' => 'required|numeric|min:0.01',
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
            'cuotas_afectadas' => 'nullable|string',
            'tipo_pago' => 'nullable|string'
        ]);

        try {
            // Validar que el crédito existe (permitimos pagos aunque no esté activo)
            // Cargar relaciones necesarias para calcular correctamente el saldo pendiente
            $credito = \App\Models\Credito::with(['recargos', 'descuentos'])->findOrFail($request->credito_id);

            // Validar que el monto no exceda el saldo pendiente
            if ($request->monto > $credito->saldo_pendiente) {
                throw new \Exception('El monto del pago no puede exceder el saldo pendiente del crédito');
            }

            $pago = $this->leymaService->registrarPago(
                $request->credito_id,
                $request->monto,
                $request->fecha_pago,
                $request->metodo_pago,
                $request->observaciones,
                $request->cuotas_afectadas,
                $request->tipo_pago
            );

            \Log::info('Pago registrado exitosamente:', ['pago_id' => $pago->id]);

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado correctamente',
                'pago' => $pago
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error al registrar pago: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Obtener un pago específico
     */
    public function showPago($id)
    {
        $pago = \App\Models\PagoCredito::with('usuario')->findOrFail($id);

        return response()->json($pago);
    }

    /**
     * Actualizar un pago
     */
    public function updatePago(Request $request, $id)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string'
        ]);

        try {
            $pago = \App\Models\PagoCredito::findOrFail($id);

            // Cargar las relaciones necesarias para calcular correctamente el saldo pendiente
            $credito = \App\Models\Credito::with(['recargos', 'descuentos'])->findOrFail($pago->credito_id);

            // Permitir eliminar pagos independientemente del estado del crédito

            // Calcular el saldo pendiente sin el pago actual
            $saldoSinEstePago = $credito->saldo_pendiente + $pago->monto;

            // Validar que el nuevo monto no exceda el saldo pendiente
            if ($request->monto > $saldoSinEstePago) {
                throw new \Exception('El monto del pago no puede exceder el saldo pendiente del crédito');
            }

            // Actualizar el pago
            $pago->update([
                'monto' => $request->monto,
                'fecha_pago' => $request->fecha_pago,
                'metodo_pago' => $request->metodo_pago,
                'observaciones' => $request->observaciones
            ]);

            // Recalcular el crédito
            $this->leymaService->recalcularCredito($credito->id);

            return response()->json([
                'success' => true,
                'message' => 'Pago actualizado correctamente',
                'pago' => $pago->load('usuario')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Eliminar un pago
     */
    public function destroyPago($id)
    {
        try {
            $pago = \App\Models\PagoCredito::findOrFail($id);
            $creditoId = $pago->credito_id;

            // Permitir eliminar el pago aunque el crédito no esté activo

            // Eliminar el pago
            $pago->delete();

            // Recalcular el crédito
            $this->leymaService->recalcularCredito($creditoId);

            return response()->json([
                'success' => true,
                'message' => 'Pago eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Obtener flujo de caja mensual
     */
    public function flujoMensual()
    {
        $flujo = $this->cajaService->obtenerFlujoMensual();

        return response()->json($flujo);
    }

    /**
     * Obtener tendencia de caja
     */
    public function tendenciaCaja()
    {
        $tendencia = $this->cajaService->obtenerTendenciaCaja();

        return response()->json($tendencia);
    }

    /**
     * Obtener resumen por períodos
     */
    public function resumenPeriodos()
    {
        $resumen = $this->cajaService->obtenerResumenPorPeriodos();

        return response()->json($resumen);
    }

    /**
     * Eliminar un crédito
     */
    public function destroy($id)
    {
        try {
            $credito = Credito::findOrFail($id);

            // Solo permitir eliminación si no tiene pagos
            if ($credito->pagos()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar un crédito que tiene pagos registrados'
                ], 422);
            }

            DB::transaction(function () use ($credito) {
                // Eliminar recargos y descuentos
                $credito->recargos()->delete();
                $credito->descuentos()->delete();

                // Eliminar el crédito
                $credito->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Crédito eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Obtener datos para DataTable de créditos
     */
    public function data()
    {
        $creditos = Credito::with(['cliente', 'pagos', 'recargos', 'descuentos'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $creditos->map(function ($credito) {
            return [
                'id' => $credito->id,
                'cliente' => [
                    'nombre' => $credito->cliente ? $credito->cliente->nombre : 'Cliente no encontrado',
                    'dni' => $credito->cliente ? $credito->cliente->dni : 'N/A'
                ],
                'monto' => (float) $credito->monto_principal,
                'tipo' => ucfirst($credito->tipo_pago),
                'dias_duracion' => $credito->dias_credito,
                'porcentaje' => (float) $credito->porcentaje_final,
                'monto_a_cobrar' => (float) $credito->monto_total,
                'fecha_inicio' => $credito->fecha_inicio ? (is_string($credito->fecha_inicio) ? $credito->fecha_inicio : date('d/m/Y', strtotime($credito->fecha_inicio))) : 'N/A',
                'estado' => ucfirst($credito->estado),
                // Campos adicionales para funcionalidad
                'numero_credito' => $credito->numero_credito,
                'monto_principal' => (float) $credito->monto_principal,
                'monto_total' => (float) $credito->monto_total,
                'monto_pagado' => (float) $credito->monto_pagado,
                'saldo_pendiente' => (float) $credito->saldo_pendiente,
                'tipo_pago' => $credito->tipo_pago,
                'cantidad_cuotas' => $credito->cantidad_cuotas,
                'fecha_vencimiento' => $credito->fecha_vencimiento ? (is_string($credito->fecha_vencimiento) ? $credito->fecha_vencimiento : date('d/m/Y', strtotime($credito->fecha_vencimiento))) : 'N/A',
                'porcentaje_final' => (float) $credito->porcentaje_final,
                'dias_credito' => $credito->dias_credito,
                'created_at' => $credito->created_at->format('d/m/Y H:i'),
                'pagos' => $credito->pagos->map(function ($pago) {
                    return [
                        'id' => $pago->id,
                        'monto' => (float) $pago->monto,
                        'fecha_pago' => $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y') : 'N/A',
                        'observaciones' => $pago->observaciones
                    ];
                })
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Mostrar formulario de creación de crédito
     */
    public function create()
    {
        $clientes = ClienteCredito::orderBy('nombre')->get();
        $usuarios = \App\Models\User::orderBy('name')->get();
        $tiposCredito = [
            ['id' => 'diario', 'nombre' => 'Diario'],
            ['id' => 'semanal', 'nombre' => 'Semanal'],
            ['id' => 'quincenal', 'nombre' => 'Quincenal'],
            ['id' => 'contado', 'nombre' => 'Contado']
        ];

        return response()->json([
            'clientes' => $clientes,
            'usuarios' => $usuarios,
            'tipos_credito' => $tiposCredito
        ]);
    }

    /**
     * Mostrar un crédito específico
     */
    public function show($id)
    {
        $credito = Credito::with(['cliente', 'pagos', 'recargos', 'descuentos'])
            ->findOrFail($id);

        return response()->json([
            'id' => $credito->id,
            'numero_credito' => $credito->numero_credito,
            'cliente' => $credito->cliente,
            'monto_principal' => (float) $credito->monto_principal,
            'monto_total' => (float) $credito->monto_total,
            'monto_pagado' => (float) $credito->monto_pagado,
            'saldo_pendiente' => (float) $credito->saldo_pendiente,
            'tipo_pago' => $credito->tipo_pago,
            'cantidad_cuotas' => $credito->cantidad_cuotas,
            'porcentaje_base' => (float) $credito->porcentaje_base,
            'fecha_inicio' => $credito->fecha_inicio,
            'fecha_vencimiento' => $credito->fecha_vencimiento,
            'estado' => $credito->estado,
            'porcentaje_final' => (float) $credito->porcentaje_final,
            'dias_credito' => $credito->dias_credito,
            'observaciones' => $credito->observaciones,
            'pagos' => $credito->pagos->map(function ($pago) {
                return [
                    'id' => $pago->id,
                    'monto' => (float) $pago->monto,
                    'fecha_pago' => $pago->fecha_pago ? $pago->fecha_pago->format('Y-m-d\TH:i:s') : null,
                    'metodo_pago' => $pago->metodo_pago,
                    'cuotas_afectadas' => $pago->cuotas_afectadas,
                    'tipo_pago' => $pago->tipo_pago,
                    'observaciones' => $pago->observaciones,
                ];
            }),
            'recargos' => $credito->recargos,
            'descuentos' => $credito->descuentos,
            'created_at' => $credito->created_at
        ]);
    }

    /**
     * Crear un nuevo crédito
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'cliente_id' => 'required|exists:clientes_credito,id',
                'usuario_id' => 'required|exists:users,id',
                'monto_principal' => 'required|numeric|min:0.01',
                'tipo_pago' => 'required|in:diario,semanal,quincenal,contado',
                'cantidad_cuotas' => 'required|integer|min:1',
                'porcentaje_base' => 'required|numeric|min:0.01',
                'fecha_inicio' => 'required|date',
                'fecha_final' => 'nullable|date|after_or_equal:fecha_inicio',
                'observaciones' => 'nullable|string'
            ]);

            $credito = $this->leymaService->crearCredito($validated);

            return response()->json([
                'success' => true,
                'message' => 'Crédito creado correctamente',
                'credito' => $credito
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el crédito: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Actualizar un crédito
     */
    public function update(Request $request, $id)
    {
        try {
            $credito = Credito::findOrFail($id);

            $validated = $request->validate([
                'usuario_id' => 'required|exists:users,id',
                'monto_principal' => 'required|numeric|min:0.01',
                'tipo_pago' => 'required|in:diario,semanal,quincenal,contado',
                'cantidad_cuotas' => 'required|integer|min:1',
                'porcentaje_base' => 'required|numeric|min:0.01',
                'fecha_inicio' => 'required|date',
                'fecha_final' => 'nullable|date|after_or_equal:fecha_inicio',
                'observaciones' => 'nullable|string'
            ]);

            // Manejar fecha_final para créditos contado
            if ($validated['tipo_pago'] === 'contado' && isset($validated['fecha_final'])) {
                $validated['fecha_vencimiento'] = $validated['fecha_final'];
            }
            unset($validated['fecha_final']);  // Remover fecha_final ya que no existe en la tabla

            $credito->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Crédito actualizado correctamente',
                'credito' => $credito
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el crédito: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Obtener pagos de un crédito
     */
    public function pagos($id)
    {
        $credito = Credito::with('pagos.usuario')->findOrFail($id);

        // Formatear los pagos para DataTables
        $pagos = $credito->pagos->map(function ($pago) {
            return [
                'id' => $pago->id,
                'fecha_pago' => $pago->fecha_pago ? $pago->fecha_pago->format('Y-m-d\TH:i:s') : null,
                'monto' => (float) $pago->monto,
                'metodo_pago' => $pago->metodo_pago,
                'cuotas_afectadas' => $pago->cuotas_afectadas,
                'tipo_pago' => $pago->tipo_pago,
                'observaciones' => $pago->observaciones,
                'usuario' => [
                    'name' => $pago->usuario ? $pago->usuario->name : 'N/A'
                ]
            ];
        });

        return response()->json([
            'data' => $pagos->toArray()
        ]);
    }

    /**
     * Obtener créditos vencidos
     */
    public function vencidos()
    {
        $creditos = Credito::vencidos()
            ->with(['cliente'])
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        return response()->json(['data' => $creditos]);
    }

    /**
     * Obtener créditos por vencer
     */
    public function porVencer()
    {
        $creditos = Credito::activos()
            ->where('fecha_vencimiento', '<=', now()->addDays(7))
            ->where('fecha_vencimiento', '>=', now())
            ->with(['cliente'])
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        return response()->json(['data' => $creditos]);
    }
}
