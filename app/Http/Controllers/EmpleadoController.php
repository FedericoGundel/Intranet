<?php

namespace App\Http\Controllers;

use App\Models\Cobrador;
use App\Models\Empleado;
use App\Models\Vendedor;
use App\Services\FacturaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class EmpleadoController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'apellido' => ['nullable', 'string', 'max:120'],
            'dni' => ['nullable', 'string', 'max:20', 'unique:empleados,dni'],
            'email' => ['nullable', 'email', 'max:150', 'unique:empleados,email'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'fecha_ingreso' => ['nullable', 'date'],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
            'rol' => ['required', Rule::in(['vendedor', 'cobrador', 'otro'])],
            // siempre vienen así
            'vendedor.meta_mensual' => ['nullable', 'numeric', 'min:0'],
            'vendedor.comision_porcentaje' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'vendedor.zona' => ['nullable', 'string', 'max:120'],
        ]);

        return DB::transaction(function () use ($validated) {
            $empleado = Empleado::create([
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'] ?? null,
                'dni' => $validated['dni'] ?? null,
                'email' => $validated['email'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'fecha_ingreso' => $validated['fecha_ingreso'] ?? null,
                'estado' => $validated['estado'] ?? 'activo',
            ]);

            $ven = $validated['vendedor'] ?? [];

            switch ($validated['rol']) {
                case 'vendedor':
                    Vendedor::updateOrCreate(
                        ['empleado_id' => $empleado->id],
                        [
                            'meta_mensual' => $ven['meta_mensual'] ?? null,
                            'comision_porcentaje' => $ven['comision_porcentaje'] ?? null,
                            'zona' => $ven['zona'] ?? null,
                        ]
                    );
                    break;

                case 'cobrador':
                    Cobrador::updateOrCreate(
                        ['empleado_id' => $empleado->id],
                        [
                            'comision_porcentaje' => $ven['comision_porcentaje'] ?? null,
                        ]
                    );
                    break;

                case 'otro':
                default:
                    // no hace nada extra
                    break;
            }

            return response()->json([
                'ok' => true,
                'message' => 'Empleado creado correctamente',
                'empleado' => $empleado->load(['vendedor', 'cobrador']),
            ], 201);
        });
    }

    public function show($id)
    {
        $empleado = Empleado::with(['vendedor', 'cobrador'])->findOrFail($id);

        // Determinar rol
        $rol = 'otro';
        if ($empleado->vendedor) {
            $rol = 'vendedor';
        } elseif ($empleado->cobrador) {
            $rol = 'cobrador';
        }

        return response()->json([
            'id' => $empleado->id,
            'nombre' => $empleado->nombre,
            'apellido' => $empleado->apellido,
            'dni' => $empleado->dni,
            'email' => $empleado->email,
            'telefono' => $empleado->telefono,
            'fecha_ingreso' => $empleado->fecha_ingreso,
            'estado' => $empleado->estado,
            // ✅ atributo único con el nombre del rol
            'rol' => $rol,
            // Datos de vendedor (si aplica)
            'vendedor' => $empleado->vendedor ? [
                'meta_mensual' => $empleado->vendedor->meta_mensual,
                'comision_porcentaje' => $empleado->vendedor->comision_porcentaje,
                'zona' => $empleado->vendedor->zona,
            ] : null,
            // Datos de cobrador (si aplica)
            'cobrador' => $empleado->cobrador ? [
                'comision_porcentaje' => $empleado->cobrador->comision_porcentaje,
            ] : null,
        ]);
    }

    public function data()
    {
        // Traemos empleados con relación vendedor, cobrador y sus nóminas
        $empleados = Empleado::with(['vendedor', 'cobrador', 'nominas.pagos'])->get();

        // Formateamos respuesta
        $data = $empleados->map(function ($emp) {
            // Calculamos deuda total (sumar cada nómina: total - total_pagado)
            $deudaTotal = $emp->nominas->sum('saldo');

            // Determinar rol
            $rol = 'otro';
            if ($emp->vendedor) {
                $rol = 'vendedor';
            } elseif ($emp->cobrador) {
                $rol = 'cobrador';
            }

            return [
                'id' => $emp->id,
                'nombre' => $emp->nombre,
                'apellido' => $emp->apellido,
                'dni' => $emp->dni,
                'email' => $emp->email,
                'telefono' => $emp->telefono,
                'estado' => $emp->estado,
                'fecha_ingreso' => $emp->fecha_ingreso,
                'rol' => $rol,
                // Datos de vendedor
                'vendedor' => $emp->vendedor ? [
                    'id' => $emp->vendedor->id,
                    'meta_mensual' => $emp->vendedor->meta_mensual,
                    'comision_porcentaje' => $emp->vendedor->comision_porcentaje,
                    'zona' => $emp->vendedor->zona,
                ] : null,
                // Datos de cobrador
                'cobrador' => $emp->cobrador ? [
                    'id' => $emp->cobrador->empleado_id,
                    'comision_porcentaje' => $emp->cobrador->comision_porcentaje,
                ] : null,
                // Deuda acumulada
                'deuda' => $deudaTotal,
            ];
        });

        return response()->json([
            'data' => $data
        ]);
    }

    public function ventas(int $empleadoId, FacturaService $facturaService)
    {
        // Traer empleado con relación vendedor
        $empleado = Empleado::with('vendedor')->findOrFail($empleadoId);

        // ✅ Validar que sea vendedor
        if (!$empleado->vendedor) {
            return response()->json([
                'message' => 'El empleado no es vendedor'
            ], 422);
        }

        // Listar facturas/cobros asociados al vendedor
        $data = $facturaService->listarFacturasCobrosPorVendedor($empleadoId);

        return response()->json([
            'vendedor' => [
                'empleado_id' => $empleado->id,
                'nombre' => trim($empleado->nombre . ' ' . $empleado->apellido),
                'rol' => 'vendedor',
            ],
            'data' => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $empleado = Empleado::with(['vendedor', 'cobrador', 'nominas'])->findOrFail($id);

        // Validación
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'dni' => 'nullable|string|max:20|unique:empleados,dni,' . $empleado->id,
            'email' => 'nullable|email|max:150|unique:empleados,email,' . $empleado->id,
            'telefono' => 'nullable|string|max:50',
            'fecha_ingreso' => 'nullable|date',
            'estado' => 'nullable|in:activo,inactivo',
            'rol' => ['required', Rule::in(['vendedor', 'cobrador', 'otro'])],
            // Datos de vendedor (siempre vienen en este formato)
            'vendedor.meta_mensual' => 'nullable|numeric|min:0',
            'vendedor.comision_porcentaje' => 'nullable|numeric|min:0|max:100',
            'vendedor.zona' => 'nullable|string|max:255',
        ]);

        $datosVendedor = $request->input('vendedor', []);
        $empleadoData = Arr::except($validated, ['vendedor', 'rol']);

        DB::transaction(function () use ($empleado, $empleadoData, $validated, $datosVendedor) {
            // Actualizar datos básicos del empleado
            $empleado->update($empleadoData);

            switch ($validated['rol']) {
                case 'vendedor':
                    // Si era cobrador antes, borramos sus datos y nóminas
                    if ($empleado->cobrador) {
                        $empleado->cobrador()->delete();
                        $empleado->nominas()->delete();
                    }

                    $payloadVend = [
                        'meta_mensual' => $datosVendedor['meta_mensual'] ?? null,
                        'comision_porcentaje' => $datosVendedor['comision_porcentaje'] ?? null,
                        'zona' => $datosVendedor['zona'] ?? null,
                    ];

                    $empleado->vendedor
                        ? $empleado->vendedor->update($payloadVend)
                        : $empleado->vendedor()->create($payloadVend);
                    break;

                case 'cobrador':
                    // Si era vendedor antes, borramos sus datos y nóminas
                    if ($empleado->vendedor) {
                        $empleado->vendedor()->delete();
                        $empleado->nominas()->delete();
                    }

                    $payloadCob = [
                        'comision_porcentaje' => $datosVendedor['comision_porcentaje'] ?? null,
                    ];

                    $empleado->cobrador
                        ? $empleado->cobrador->update($payloadCob)
                        : $empleado->cobrador()->create($payloadCob);
                    break;

                case 'otro':
                default:
                    // Si tenía rol antes, eliminamos registros y nóminas
                    if ($empleado->vendedor) {
                        $empleado->vendedor()->delete();
                        $empleado->nominas()->delete();
                    }
                    if ($empleado->cobrador) {
                        $empleado->cobrador()->delete();
                        $empleado->nominas()->delete();
                    }
                    break;
            }
        });

        // Refrescar relaciones
        $empleado->load(['vendedor', 'cobrador']);

        return response()->json([
            'message' => 'Empleado actualizado correctamente',
            'empleado' => $empleado,
        ]);
    }

    /**
     * Eliminar empleado.
     */
    public function destroy($id)
    {
        $empleado = Empleado::findOrFail($id);
        $empleado->delete();

        return response()->json([
            'message' => 'Empleado eliminado correctamente'
        ]);
    }

    public function dataTom(Request $request)
    {
        $tipo = $request->string('tipo')->toString();  // puede venir "vendedor" o "cobrador"

        $query = Empleado::query()->with(['vendedor', 'cobrador']);

        if ($tipo === 'vendedor') {
            $query->whereHas('vendedor');
        } elseif ($tipo === 'cobrador') {
            $query->whereHas('cobrador');
        }

        $empleados = $query->orderBy('nombre')->get();

        return response()->json(
            $empleados->map(function ($emp) {
                // Definir rol según relaciones
                if ($emp->vendedor) {
                    $rol = 'vendedor';
                } elseif ($emp->cobrador) {
                    $rol = 'cobrador';
                } else {
                    $rol = 'otro';
                }

                return [
                    'value' => $emp->id,
                    'text' => $emp->nombre,
                    'rol' => $rol,  // 👈 acá va el rol
                    'vendedor' => $emp->vendedor ? [
                        'meta_mensual' => $emp->vendedor->meta_mensual,
                        'comision_porcentaje' => $emp->vendedor->comision_porcentaje,
                        'zona' => $emp->vendedor->zona,
                    ] : null,
                    'cobrador' => $emp->cobrador ? [
                        'comision_porcentaje' => $emp->cobrador->comision_porcentaje,
                    ] : null,
                ];
            })
        );
    }
}
