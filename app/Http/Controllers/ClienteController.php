<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('clientes');
    }
    public function data()
    {
        // Si necesitás paginación simple:
        $clientes = Cliente::all();

        return response()->json([
            'data' => $clientes // DataTables espera que los datos estén en "data"
        ]);
    }

    public function dataTom(Request $request)
    {
        $q = $request->input('q');

        $query = Cliente::query();

        if ($q) {
            $query->where('nombre', 'like', "%{$q}%")
                ->orWhere('apellido1', 'like', "%{$q}%")
                ->orWhere('apellido2', 'like', "%{$q}%");
        }

        // Si no hay $q, esto devuelve todos los clientes (o límite para no saturar)
        $clientes = $query->get();

        return response()->json(
            $clientes->map(function ($cliente) {
                return [
                    'value' => $cliente->id,
                    'text'  => "{$cliente->nombre} {$cliente->apellido1} {$cliente->apellido2}",
                ];
            })
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'nif' => 'nullable|string|max:255|unique:clientes,nif',
            'apellido1' => 'nullable|string|max:255',
            'apellido2' => 'nullable|string|max:255',
            'tipo_cliente' => 'nullable|in:empresa,autonomo,particular',
            'direccion' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:255',
            'localidad' => 'nullable|string|max:255',
            'provincia' => 'nullable|string|max:255',
            'pais' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:255',
        ]);

        $cliente = Cliente::create($validated);

        return response()->json([
            'message' => 'Cliente creado correctamente',
            'cliente' => $cliente
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        return response()->json($cliente);
    }
    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido1' => 'nullable|string|max:255',
            'apellido2' => 'nullable|string|max:255',
            'tipo_cliente' => 'nullable|string|max:255',
            'nif' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:20',
            'localidad' => 'nullable|string|max:255',
            'provincia' => 'nullable|string|max:255',
            'pais' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string|max:20',
        ]);

        $cliente->update($validated);

        return response()->json(['message' => 'Cliente actualizado correctamente']);
    }



    public function destroy($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $cliente->delete();

        return response()->json(['message' => 'Cliente eliminado correctamente']);
    }
}
