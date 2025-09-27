<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    public function index($id = null)
    {
        if ($id) {
            $usuario = User::findOrFail($id);
        } else {
            $usuario = Auth::user();
        }

        return view('perfil', compact('usuario'));
    }
    public function show($id = null)
    {
        $userId = ($id == 0 || $id === null) ? Auth::id() : $id;

        $user = User::findOrFail($userId);

        return response()->json($user);
    }



    public function updateFondo(Request $request)
    {
        // Validación de la imagen
        $request->validate([
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tipo'   => 'required|in:foto,fondo',
        ]);

        // ID del usuario (0 = autenticado)
        $id = $request->input('id');
        $usuario = User::findOrFail($id);

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $tipo = $request->input('tipo');

        if (!$request->hasFile('imagen')) {
            // Eliminar imagen actual si existe
            if ($tipo === 'foto' && $usuario->imagen) {
                Storage::disk('public')->delete($usuario->imagen);
                $usuario->imagen = null;
            } elseif ($tipo === 'fondo' && $usuario->imagen_fondo) {
                Storage::disk('public')->delete($usuario->imagen_fondo);
                $usuario->imagen_fondo = null;
            }
        } else {
            // Subir nueva imagen
            $imagePath = $request->file('imagen')->store('users', 'public');

            if ($tipo === 'foto') {
                // borrar la vieja si había
                if ($usuario->imagen) {
                    Storage::disk('public')->delete($usuario->imagen);
                }
                $usuario->imagen = $imagePath;
            } elseif ($tipo === 'fondo') {
                if ($usuario->imagen_fondo) {
                    Storage::disk('public')->delete($usuario->imagen_fondo);
                }
                $usuario->imagen_fondo = $imagePath;
            }
        }

        $usuario->save();

        return response()->json([
            'message'       => 'Imagen actualizada correctamente',
            'imagen'        => $usuario->imagen ? asset('storage/' . $usuario->imagen) : null,
            'imagen_fondo'  => $usuario->imagen_fondo ? asset('storage/' . $usuario->imagen_fondo) : null,
        ]);
    }
}