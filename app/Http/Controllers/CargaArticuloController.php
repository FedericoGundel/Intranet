<?php

namespace App\Http\Controllers;

use App\Models\CargaArticulo;
use Illuminate\Http\Request;
use App\Services\CargaArticuloService;
use Illuminate\Validation\ValidationException;

class CargaArticuloController extends Controller
{
    public function data()
    {
        // Si querés traer también el artículo relacionado
        $cargas = CargaArticulo::with('articulo')->get();

        return response()->json($cargas);
    }
    public function getByArticulo($id)
    {
        // Trae todas las cargas de un artículo específico con su relación
        $cargas = CargaArticulo::with('articulo')
            ->where('id_articulo', $id)
            ->get();

        return response()->json($cargas);
    }
    public function store(Request $request, CargaArticuloService $service)
    {
        $data = $request->validate([
            'id_articulo' => ['required', 'integer', 'exists:articulos,id'],
            'cantidad'    => ['required', 'integer', 'min:1'],
            'fecha'       => ['nullable', 'date'],
        ]);

        $carga = $service->crear($data);

        return response()->json([
            'message' => 'Carga creada y stock aumentado',
            'data'    => $carga,
        ], 201);
    }

    // (Opcional) Revertir
    public function destroy(CargaArticulo $carga, CargaArticuloService $service)
    {
        $service->revertir($carga);

        return response()->json([
            'message' => 'Carga revertida y stock descontado',
        ]);
    }

    // (Opcional) Actualizar cantidad/fecha

}