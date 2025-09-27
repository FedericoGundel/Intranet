<?php

namespace App\Http\Controllers;

use App\Services\NominaService;
use Illuminate\Http\Request;

class NominaController extends Controller
{
    public function __construct(
        private NominaService $service
    ) {
        //
    }

    /**
     * Vista principal
     */
    public function index()
    {
        return view('nominas');
    }

    public function destroy($id)
    {
        $this->service->eliminarNomina((int) $id);

        return response()->json([
            'success' => true,
            'message' => 'Nómina eliminada correctamente',
        ]);
    }

    /**
     * DataTable genérico: /nominas/data (+ filtros opcionales por querystring)
     */
    public function data(Request $request)
    {
        $nominas = $this->service->listarNominas($request);
        return response()->json(['data' => $nominas]);
    }

    /**
     * Listar nóminas por empleado: /empleados/{id}/nominas
     */
    public function porEmpleado(int $empleadoId)
    {
        $nominas = $this->service->listarNominasPorEmpleado($empleadoId);
        return response()->json(['data' => $nominas]);
    }

    /** Preview de cálculo (no crea): POST /nominas/preview */

    /**
     * Crear/actualizar (upsert) nómina de un empleado en un rango: POST /nominas/upsert
     */
    public function upsert(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'desde' => 'required|date',
            'hasta' => 'required|date|after_or_equal:desde',
            'periodo' => 'nullable|string|max:10',
            'ajustes' => 'nullable|numeric',
        ]);

        $nomina = $this->service->upsert(
            empleadoId: (int) $request->empleado_id,
            desde: $request->desde,
            hasta: $request->hasta,
            periodo: $request->input('periodo'),
            ajustes: (float) $request->input('ajustes', 0)
        );

        return response()->json([
            'message' => 'Nómina guardada en borrador.',
            'nomina' => $nomina,
        ]);
    }

    /**
     * Cerrar nómina: POST /nominas/{id}/cerrar
     */
    public function cerrar(int $id)
    {
        $nomina = $this->service->cerrar($id);
        return response()->json(['message' => 'Nómina cerrada.', 'nomina' => $nomina]);
    }

    /**
     * Registrar pago: POST /nominas/{id}/pagar
     */
    public function pagar(Request $request, int $id)
    {
        $request->validate([
            'fecha' => 'required|date',
            'importe' => 'required|numeric|min:0.01',
            'metodo' => 'nullable|string|max:100',
            'nota' => 'nullable|string|max:255',
        ]);

        $nomina = $this->service->pagar(
            nominaId: $id,
            fecha: $request->fecha,
            importe: (float) $request->importe,
            metodo: $request->input('metodo'),
            nota: $request->input('nota')
        );

        return response()->json(['message' => 'Pago registrado.', 'nomina' => $nomina]);
    }

    /**
     * Agregar ajuste: POST /nominas/{id}/ajustes
     */
    public function agregarAjuste(Request $request, int $id)
    {
        $request->validate([
            'descripcion' => 'nullable|string|max:255',
            'importe' => 'required|numeric',
        ]);

        $nomina = $this->service->agregarAjuste(
            nominaId: $id,
            descripcion: $request->input('descripcion'),
            importe: (float) $request->importe
        );

        return response()->json(['message' => 'Ajuste agregado.', 'nomina' => $nomina]);
    }

    /**
     * Detalle completo: GET /nominas/{id}
     */
    public function show(int $id)
    {
        $nomina = $this->service->show($id);
        return response()->json($nomina);
    }
}
