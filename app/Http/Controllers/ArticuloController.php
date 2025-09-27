<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticuloController extends Controller
{
    public function index()
    {
        $articulos = Articulo::all();
        return view('articulos', compact('articulos'));
    }

    public function data()
    {
        // Si necesitás paginación simple:
        $clientes = Articulo::all();

        return response()->json([
            'data' => $clientes  // DataTables espera que los datos estén en "data"
        ]);
    }

    public function show($id)
    {
        $cliente = Articulo::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Artículo no encontrado'], 404);
        }

        return response()->json($cliente);
    }

    public function dataTom(Request $request)
    {
        $q = $request->input('q');

        $query = Articulo::query();

        if ($q) {
            $query
                ->where('nombre', 'like', "%{$q}%")
                ->orWhere('codigo', 'like', "%{$q}%");
        }

        // Si no hay $q, esto devuelve todos los clientes (o límite para no saturar)
        $clientes = $query->get();

        return response()->json(
            $clientes->map(function ($cliente) {
                return [
                    'value' => $cliente->id,
                    'text' => "{$cliente->nombre} {$cliente->codigo}",
                ];
            })
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'descuento' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'codigo' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('articulos', 'public');
        }

        Articulo::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Artículo creado correctamente.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $articulo = Articulo::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'stock' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'precio' => 'nullable|numeric',
            'descuento' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|max:2048',
        ]);

        // Si el usuario marcó que quiere eliminar la imagen
        if ($request->boolean('eliminar_imagen')) {
            if ($articulo->imagen && Storage::disk('public')->exists($articulo->imagen)) {
                Storage::disk('public')->delete($articulo->imagen);
            }
            $validated['imagen'] = null;
        }

        // Si se sube una nueva imagen
        if ($request->hasFile('imagen')) {
            if ($articulo->imagen && Storage::disk('public')->exists($articulo->imagen)) {
                Storage::disk('public')->delete($articulo->imagen);
            }
            $validated['imagen'] = $request->file('imagen')->store('articulos', 'public');
        }

        $articulo->update($validated);

        return response()->json(['message' => 'Artículo actualizado correctamente']);
    }

    public function destroy($id)
    {
        $articulo = Articulo::findOrFail($id);

        // Si hay imagen, la eliminamos del almacenamiento
        if ($articulo->imagen) {
            Storage::disk('public')->delete($articulo->imagen);
        }

        $articulo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Artículo eliminado correctamente. Las facturas mantendrán la información del producto y las cargas de stock se eliminarán automáticamente.'
        ]);
    }
}