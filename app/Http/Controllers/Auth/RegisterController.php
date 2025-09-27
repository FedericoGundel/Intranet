<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\Models\Configuration;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    // >>> Sobrescribe SOLO este método <<<
    public function register(Request $request)
    {
        // Validación
        $this->validator($request->all())->validate();

        // Crear usuario
        $user = $this->create($request->all());

        // 🔹 Asignar rol por defecto desde la tabla configuraciones
        try {
            $defaultRoleId = (int) Configuration::getValue('rol_defecto'); // p.ej. 1
            if ($defaultRoleId) {
                $role = Role::find($defaultRoleId);

                if ($role) {
                    // Asegurar guard compatible (evita GuardDoesNotMatch)
                    $userGuard = config('auth.defaults.guard', 'web');


                    if ($role->guard_name === $userGuard) {
                        $user->assignRole($role); // acepta modelo Role
                    } else {
                        \Log::warning('No se asignó rol por defecto: guard distinto.', [
                            'user_id' => $user->id,
                            'role_id' => $role->id,
                            'role_guard' => $role->guard_name,
                            'user_guard' => $userGuard,
                        ]);
                    }
                } else {
                    \Log::warning('Rol por defecto configurado no existe.', [
                        'default_role_id' => $defaultRoleId
                    ]);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Fallo asignando rol por defecto al registrarse.', [
                'user_id' => $user->id ?? null,
                'error'   => $e->getMessage(),
            ]);
        }

        // Disparar evento Registered (si usás verificación de email, etc.)
        event(new Registered($user));

        // No loguear automáticamente
        return redirect()
            ->route('login')
            ->with('status', 'Cuenta creada correctamente. Iniciá sesión para continuar.');
    }

    // <<< Fin override >>>

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}