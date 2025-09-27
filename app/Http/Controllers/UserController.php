<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Solo permite acceso a admins (podés proteger esto con middleware si lo necesitás)
    public function __construct()
    {
        $this->middleware('auth'); // y/o $this->middleware('admin');
    }

    /**
     * Muestra usuarios que aún no han sido aprobados.
     */


    /**
     * Aprueba un usuario por ID.
     */
public function approve($id)
{
    $user = User::findOrFail($id);
    $user->approved = true;
    $user->save();

    return response()->json(['success' => true, 'message' => 'Usuario aprobado correctamente.']);
}

public function reject($id)
{
    $user = User::findOrFail($id);
    $user->approved = false; // o null si querés diferenciar "rechazado"
    $user->save();

    return response()->json(['success' => true, 'message' => 'Usuario rechazado correctamente.']);
}

public function destroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente.']);
}



    /**
     * (Opcional) Lista de todos los usuarios.
     */
    public function index()
    {
        $users = User::all();
        return view('users', compact('users'));
    }

       public function data()
    {
        // Si necesitás paginación simple:
        $clientes = User::all();

        return response()->json([
            'data' => $clientes // DataTables espera que los datos estén en "data"
        ]);
    }
}
